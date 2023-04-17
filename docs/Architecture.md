# Hexagonal Architecture

I've decided to use hexagonal architecture in this case. This architecture is perfect in case of separating domain from infrastructure. I have also separated UI from main application parts. In this scenario user interface can be developed separately, and it's almost independent of domain and infrastructure


# CQRS

CQRS was used in case of separating "writes" and "reads" in application layer. In DDD scenario, commands should make changes through domain model.
