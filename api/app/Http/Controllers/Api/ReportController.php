<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComparativeReportRequest;
use App\Http\Requests\ReportDateRangeRequest;
use App\Http\Resources\ReportResource;
use App\Services\ReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use AuthorizesRequests;

    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display dashboard overview.
     * GET /api/reports/dashboard
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewDashboard');

        $data = $this->reportService->getDashboardData();

        return (new ReportResource($data))->response();
    }

    /**
     * Display sales report.
     * GET /api/reports/sales
     */
    public function sales(ReportDateRangeRequest $request): JsonResponse
    {
        $this->authorize('viewSalesReport');

        $data = $this->reportService->getSalesReport(
            $request->getStartDate(),
            $request->getEndDate()
        );

        return (new ReportResource($data))->response();
    }

    /**
     * Display product performance report.
     * GET /api/reports/products
     */
    public function products(ReportDateRangeRequest $request): JsonResponse
    {
        $this->authorize('viewProductPerformance');

        $data = $this->reportService->getProductPerformance(
            $request->getStartDate(),
            $request->getEndDate()
        );

        return (new ReportResource($data))->response();
    }

    /**
     * Display customer insights report.
     * GET /api/reports/customers
     */
    public function customers(ReportDateRangeRequest $request): JsonResponse
    {
        $this->authorize('viewCustomerInsights');

        $data = $this->reportService->getCustomerInsights(
            $request->getStartDate(),
            $request->getEndDate()
        );

        return (new ReportResource($data))->response();
    }

    /**
     * Display inventory report.
     * GET /api/reports/inventory
     */
    public function inventory(): JsonResponse
    {
        $this->authorize('viewInventoryReport');

        $data = $this->reportService->getInventoryReport();

        return (new ReportResource($data))->response();
    }

    /**
     * Display financial summary report.
     * GET /api/reports/financial
     */
    public function financial(ReportDateRangeRequest $request): JsonResponse
    {
        $this->authorize('viewFinancialSummary');

        $data = $this->reportService->getFinancialSummary(
            $request->getStartDate(),
            $request->getEndDate()
        );

        return (new ReportResource($data))->response();
    }

    /**
     * Display comparative report.
     * GET /api/reports/comparative
     */
    public function comparative(ComparativeReportRequest $request): JsonResponse
    {
        $this->authorize('viewComparativeReport');

        $data = $this->reportService->getComparativeReport(
            $request->getPeriod1Start(),
            $request->getPeriod1End(),
            $request->getPeriod2Start(),
            $request->getPeriod2End()
        );

        return (new ReportResource($data))->response();
    }
}
