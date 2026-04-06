# advanced-distributed-systems-with-laravel

This project demonstrates horizontal database scaling in Laravel. By decoupling data into multiple shards using a routing logic, we significantly reduce I/O bottlenecks. This architecture is essential for fintech or high-traffic platforms (like bKash or Nagad) handling millions of concurrent transactions.