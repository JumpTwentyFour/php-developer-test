
Further coding bits that may help this solution:

## Failures

- upon API failures when running the code what is the plan? Notification going over to dev+ops teams, logging, supplier call-out?
- upon failure, would the db hold up on a rollback? Should each upsert be its own transaction or will a rollback create so many locks that it would impact regular usage?

## Data ingested

- the data added to the Users need to be analysed from the point of indexes to make sure that anything being queried is properly fetched using as much indexing as possible/usable/required. Over-indexing should also be taken into consideration if the User table has too many fields.
- if the amount of data being updated/inserted is too much a bulk execution should be considered or even a downtime window

## Unit tests

- unit tests with mocked endpoints for the service (Http::fake)
-- run this in the regular pipeline
- unit tests in a separate suite that will hit the actual API service to validate it and manage its uptime
-- this should be outside of the regular pipeline to avoid snags and API outages
-- alerts/notifications should be in place for this to make sure observability of the service is high depending (slack, email, etc...)
- unit test would be:
-- no pages defined to fetch
-- all pages to fetch
-- specific page number to fetch
-- 

## UseCase

- if the specs require usage of the same functionality that the Command has of upserting the User then the code can be moved to a UseCase. This way the functionality can be used by any other UseCase, Command, Controller, etc... when needed.

## Audit trail

- if observability is needed, add a log of the updates that are done (log file only)
-- if further audit is needed a timestamp change table can be created to log any change done to the User record (a poly table would be ideal so other records can benefit from it)
-- if audit code starts cluttering too much then it can be moved to an Observer

## Docs

- at least a high-level, non-techinical description doc explaining what goes on in this code and details for it like scheduling, requirements, 3rd party doc links, etc...

## Supplier info

- the API may fail if enough calls are made so throtling may need to be implemented (avoids blacklisting, IP delaying, etc...). This needs to be checked with the supplier to make sure no limits are crossed.

## Deployment

- devops notifications and awareness - since this will run hourly they need to know what will create processing spikes in the box and db

## Logging 

- when the process end, what should happen? Any specifics into who needs to be notified or what systems need to have this information (Cloudwatch, Grafana, Datadog, etc...)


