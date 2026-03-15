<?php

namespace App\Console\Commands;

use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneratePostpaidBillingStatements extends Command
{
    protected $signature = 'billing:generate-postpaid
                            {--date= : 기준일(YYYY-MM-DD)}
                            {--shop_id= : 특정 화원사만 처리}
                            {--force : 이미 같은 기간 청구서가 있어도 생성}';

    protected $description = '후불 화원사 청구서를 생성합니다. 테스트용: 이번 달 1일 ~ 기준일까지 집계';

    public function handle(): int
    {
        $baseDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $periodStart = $baseDate->copy()->startOfMonth()->toDateString();
        $periodEnd = $baseDate->copy()->toDateString();

        $this->info('후불 청구서 생성 시작');
        $this->line('기준일: ' . $baseDate->toDateString());
        $this->line('집계기간: ' . $periodStart . ' ~ ' . $periodEnd);

        $shopQuery = Shop::query()
            ->where('point_policy_type', 'postpaid');

        if ($this->option('shop_id')) {
            $shopQuery->where('id', (int) $this->option('shop_id'));
        }

        $shops = $shopQuery->orderBy('id')->get();

        if ($shops->isEmpty()) {
            $this->warn('대상 후불 화원사가 없습니다.');
            return self::SUCCESS;
        }

        foreach ($shops as $shop) {
            DB::transaction(function () use ($shop, $periodStart, $periodEnd) {
                $exists = DB::table('billing_statements')
                    ->where('shop_id', $shop->id)
                    ->whereDate('period_start', $periodStart)
                    ->whereDate('period_end', $periodEnd)
                    ->exists();

                if ($exists && !$this->option('force')) {
                    $this->warn("shop_id={$shop->id} 이미 같은 기간 청구서가 있어 건너뜀");
                    return;
                }

                $transactions = DB::table('point_transactions as pt')
                    ->leftJoin('billing_statement_items as bsi', 'pt.id', '=', 'bsi.point_transaction_id')
                    ->where('pt.shop_id', $shop->id)
                    ->where('pt.transaction_type', 'order_debit')
                    ->whereDate('pt.transacted_at', '>=', $periodStart)
                    ->whereDate('pt.transacted_at', '<=', $periodEnd)
                    ->whereNull('bsi.id')
                    ->orderBy('pt.id')
                    ->select([
                        'pt.id',
                        'pt.order_id',
                        'pt.amount',
                        'pt.summary',
                        'pt.description',
                        'pt.transacted_at',
                    ])
                    ->get();

                if ($transactions->isEmpty()) {
                    $this->line("shop_id={$shop->id} 청구 대상 거래 없음");
                    return;
                }

                $debitTotal = (int) $transactions->sum('amount');
                $creditTotal = 0;
                $adjustTotal = 0;
                $invoiceAmount = $debitTotal - $creditTotal + $adjustTotal;

                $statementNo = $this->generateStatementNo($shop->id, $periodStart, $periodEnd);

                $billingStatementId = DB::table('billing_statements')->insertGetId([
                    'shop_id' => $shop->id,
                    'statement_no' => $statementNo,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'debit_total' => $debitTotal,
                    'credit_total' => $creditTotal,
                    'adjust_total' => $adjustTotal,
                    'invoice_amount' => $invoiceAmount,
                    'paid_amount' => 0,
                    'status' => 'draft',
                    'issued_at' => null,
                    'due_date' => null,
                    'paid_at' => null,
                    'tax_invoice_no' => null,
                    'memo' => '테스트 청구서 자동 생성',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $itemRows = [];

                foreach ($transactions as $tx) {
                    $itemRows[] = [
                        'billing_statement_id' => $billingStatementId,
                        'point_transaction_id' => $tx->id,
                        'order_id' => $tx->order_id,
                        'amount' => $tx->amount,
                        'summary' => $tx->summary ?: '후불 발주 차감',
                        'description' => $tx->description,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                DB::table('billing_statement_items')->insert($itemRows);

                $this->info(
                    "shop_id={$shop->id} 청구서 생성 완료 | statement_no={$statementNo} | 건수="
                    . count($itemRows)
                    . " | 금액={$invoiceAmount}"
                );
            });
        }

        $this->info('후불 청구서 생성 종료');

        return self::SUCCESS;
    }

    protected function generateStatementNo(int $shopId, string $periodStart, string $periodEnd): string
    {
        return 'BS'
            . Carbon::parse($periodStart)->format('Ym')
            . sprintf('%05d', $shopId)
            . strtoupper(Str::random(4));
    }
}
