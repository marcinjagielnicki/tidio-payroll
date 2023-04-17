

# Tidio-Payroll

For more information about DDD and architecture please refer to docs directory

## Installation

```bash
make start
make migrations
make fixtures
```

Application will be available at http://localhost:5000

Payroll report url: http://localhost:5000/api/payroll/report

Available GET options:

- sortBy (one of: uuid,
  firstName,
  lastName,
  totalSalary,
  additionToBase,
  department.name,
  remunerationBase,
  department.bonusType)
- sortByDirection (ASC, DESC)
- filter (value to filter)
- filterBy (uuid, firstName, lastName, departmentName)



## Testing

```bash
make phpunit
```

## Code-Style and Static Analysis

Check code style:
```bash
make cs
```

Fix code style
```bash
make cs-fix
```

Run static analysis:
```bash
make phpstan
```
