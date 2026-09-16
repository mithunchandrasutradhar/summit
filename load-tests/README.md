# Load testing

`peak-traffic.js` is a [k6](https://k6.io) script simulating expected peak
concurrency — registration opening, a viral social moment, or the days
around the Grand Summit itself. It is **not run automatically** as part of
the test suite; it's a pre-launch checklist item to run manually against a
staging environment.

## Running

Install k6 (not part of this repo's dependencies — it's a separate binary):
<https://grafana.com/docs/k6/latest/set-up/install-k6/>

```
BASE_URL=https://staging.freelancersummit.bd k6 run load-tests/peak-traffic.js
```

Never run this against production or a shared dev environment without
warning whoever else depends on it — 300 concurrent virtual users is
enough to knock over an under-provisioned box.

## What it checks

- `/en/register`, `/en/awards/nominate`, `/en/sponsorship-opportunity` stay
  under a 1.5s p95 response time and a <1% error rate through a ramp to 300
  concurrent virtual users.
- Optionally, a real payment-initiation signed URL (seed one in staging
  first — see the comment at the top of the script) redirects correctly
  under load.

## When to run it

- Before the registration window opens.
- Before the Grand Summit date itself.
- After any significant infrastructure change (PHP-FPM pool size, queue
  worker count, database tier).

If it fails the thresholds, the fix is almost always one of: PHP-FPM
`pm.max_children`, MySQL connection pool size, Redis/queue worker count, or
enabling the Cloudflare caching described in the deployment notes for public
pages — not application code.
