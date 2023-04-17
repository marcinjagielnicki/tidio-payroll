# DDD
I've decided to use some sort of DDD approach in this test task. I consider DDD to be perfect in case of highly business-oriented logic related to addition to base calculation.

I have separated 3 bounded context: Employee, Department and Payroll. At this moment Employee and Department are used only as Write Models and data storing.

Payroll BC has implemented logic to calculate salary bonus based on Employee's Department.

### Bonus is calculated in this way:

IF department.additionToBaseType = years_of_working then we multiply first 10 years of seniority by bonus amount declared in cents amount and add it base salary

IF department.additionToBaseType = percentage then we add to Employee's base salary declared percentage of base salary
