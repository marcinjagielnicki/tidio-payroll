<?php

declare(strict_types=1);

namespace UI\Http\Rest\Controller\Payroll;

use App\Payroll\Application\Query\ListPayroll\ListPayrollQuery;
use App\Payroll\Domain\Filter\PayrollFilter;
use App\Payroll\Domain\Payroll;
use App\Payroll\Domain\Sort\PayrollSort;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use UI\Http\Rest\Controller\QueryController;
use UI\Http\Rest\View\PayrollView;
use Webmozart\Assert\Assert;

class PayrollReportController extends QueryController
{
    public function __construct(
        QueryBusInterface $queryBus,
        private readonly SerializerInterface $serializer
    ) {
        parent::__construct($queryBus);
    }

    #[Route(path: 'payroll/report', name: 'payroll_report', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $sortBy = $request->query->get('sortBy');
        $sortByDirection = $request->query->get('sortByDirection');

        $filterBy = $request->query->get('filterBy');
        $filterValue = $request->query->get('filter');

        $payrollFilter = null;
        $payrollSort = null;

        if ($filterBy && $filterValue) {
            Assert::string($filterBy);
            $payrollFilter = PayrollFilter::createFromColumnNameAndValue($filterBy, $filterValue);
        }

        if ($sortBy && $sortByDirection) {
            Assert::string($sortBy);
            Assert::string($sortByDirection);
            $payrollSort = new PayrollSort($sortBy, $sortByDirection);
        }

        $listPayrollQuery = new ListPayrollQuery($payrollSort, $payrollFilter);

        /** @var Payroll[] $payroll */
        $payroll = $this->ask($listPayrollQuery);

        $payrollView = array_map(fn (Payroll $payroll) => PayrollView::fromPayroll($payroll), $payroll);

        return new JsonResponse($this->serializer->serialize($payrollView, 'json'), 200, [], true);
    }
}
