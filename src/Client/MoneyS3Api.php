<?php

declare(strict_types=1);

namespace VsPoint\MoneyS3\Client;

use VsPoint\MoneyS3\Agenda\AgendaListService;
use VsPoint\MoneyS3\Agenda\BankStatementService;
use VsPoint\MoneyS3\Agenda\CashVoucherService;
use VsPoint\MoneyS3\Agenda\IssuedInvoiceService;
use VsPoint\MoneyS3\Agenda\IssuedOrderService;
use VsPoint\MoneyS3\Agenda\IssuedSlipService;
use VsPoint\MoneyS3\Agenda\JournalEntryService;
use VsPoint\MoneyS3\Agenda\JournalTransactionService;
use VsPoint\MoneyS3\Agenda\ReceivedInvoiceService;
use VsPoint\MoneyS3\Agenda\ReceivedOrderService;
use VsPoint\MoneyS3\Agenda\ReceivedSlipService;
use VsPoint\MoneyS3\Agenda\StockTakingDocumentService;
use VsPoint\MoneyS3\Agenda\WageService;
use VsPoint\MoneyS3\Agenda\WarehouseStockService;
use VsPoint\MoneyS3\Transport\Transport;

/**
 * The Money S3 API facade — exposes one strictly typed service per agenda over a
 * configured {@see Transport}.
 *
 * Usually obtained through a concrete connection ({@see Client} for a local instance or
 * {@see CloudClient} for `*.api.moneys3.eu`), but can also be constructed directly with
 * any {@see Transport} (e.g. in tests).
 */
class MoneyS3Api
{
    /**
     * Issued invoices (faktura vystavená) — read / create / delete.
     */
    public readonly IssuedInvoiceService $issuedInvoices;

    /**
     * Received invoices (faktura přijatá) — read / create / delete.
     */
    public readonly ReceivedInvoiceService $receivedInvoices;

    /**
     * Bank documents (bankovní doklad) — read / create.
     */
    public readonly BankStatementService $bankStatements;

    /**
     * Cash documents (pokladní doklad) — read / create.
     */
    public readonly CashVoucherService $cashVouchers;

    /**
     * Received orders (objednávka přijatá) — read / create.
     */
    public readonly ReceivedOrderService $receivedOrders;

    /**
     * Issued orders (objednávka vystavená) — read / create.
     */
    public readonly IssuedOrderService $issuedOrders;

    /**
     * Warehouse received slips (skladová příjemka) — create.
     */
    public readonly ReceivedSlipService $receivedSlips;

    /**
     * Warehouse issued slips (skladová výdejka) — create.
     */
    public readonly IssuedSlipService $issuedSlips;

    /**
     * Stock-taking documents (inventurní doklad) — create.
     */
    public readonly StockTakingDocumentService $stockTakingDocuments;

    /**
     * Wages (mzda) — create.
     */
    public readonly WageService $wages;

    /**
     * Cash journal (peněžní deník) — read.
     */
    public readonly JournalTransactionService $cashJournal;

    /**
     * Accounting journal (účetní deník) — read.
     */
    public readonly JournalEntryService $accountingJournal;

    /**
     * Warehouse stock cards (skladová zásoba) — read.
     */
    public readonly WarehouseStockService $warehouseStocks;

    /**
     * Agendas / accounting units — read.
     */
    public readonly AgendaListService $agendas;

    public function __construct(
        public readonly Transport $transport,
    ) {
        $this->issuedInvoices = new IssuedInvoiceService($transport);
        $this->receivedInvoices = new ReceivedInvoiceService($transport);
        $this->bankStatements = new BankStatementService($transport);
        $this->cashVouchers = new CashVoucherService($transport);
        $this->receivedOrders = new ReceivedOrderService($transport);
        $this->issuedOrders = new IssuedOrderService($transport);
        $this->receivedSlips = new ReceivedSlipService($transport);
        $this->issuedSlips = new IssuedSlipService($transport);
        $this->stockTakingDocuments = new StockTakingDocumentService($transport);
        $this->wages = new WageService($transport);
        $this->cashJournal = new JournalTransactionService($transport);
        $this->accountingJournal = new JournalEntryService($transport);
        $this->warehouseStocks = new WarehouseStockService($transport);
        $this->agendas = new AgendaListService($transport);
    }
}
