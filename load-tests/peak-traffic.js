/**
 * k6 load test for expected peak-concurrency moments: registration opening,
 * a viral social moment, and the days immediately around the Grand Summit
 * itself (brief §2/§22 — this is a national, government-linked campaign
 * with real spike risk).
 *
 * Run against a staging/production URL — never against a shared dev
 * environment other people are using:
 *
 *   BASE_URL=https://staging.freelancersummit.bd k6 run load-tests/peak-traffic.js
 *
 * To also exercise the payment-initiate redirect, seed a real
 * SponsorshipEnquiry/ExhibitorApplication in staging first and pass its
 * signed URL explicitly (it can't be fabricated by this script, since it's
 * a Laravel-signed URL tied to a real record):
 *
 *   PAYMENT_URL="https://staging.../en/sponsorship-opportunity/pay/123?signature=..." k6 run load-tests/peak-traffic.js
 *
 * Adjust the `stages` below to the concurrency you actually expect — the
 * numbers here are a starting-point guess, not a validated target.
 */

import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Rate } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://localhost';
const PAYMENT_URL = __ENV.PAYMENT_URL || null;

export const errorRate = new Rate('errors');

export const options = {
    stages: [
        { duration: '1m', target: 50 },   // ramp-up
        { duration: '3m', target: 300 },  // expected peak (registration opening / viral moment)
        { duration: '2m', target: 300 },  // sustained peak
        { duration: '1m', target: 0 },    // ramp-down
    ],
    thresholds: {
        http_req_failed: ['rate<0.01'],
        http_req_duration: ['p(95)<1500'],
        errors: ['rate<0.01'],
    },
};

export default function () {
    group('Summit registration page', function () {
        const res = http.get(`${BASE_URL}/en/register`);
        const ok = check(res, { 'register page is 200': (r) => r.status === 200 });
        errorRate.add(!ok);
    });

    sleep(1);

    group('Award nomination page', function () {
        const res = http.get(`${BASE_URL}/en/awards/nominate`);
        const ok = check(res, { 'award nominate page is 200': (r) => r.status === 200 });
        errorRate.add(!ok);
    });

    sleep(1);

    group('Sponsorship opportunity page', function () {
        const res = http.get(`${BASE_URL}/en/sponsorship-opportunity`);
        const ok = check(res, { 'sponsorship page is 200': (r) => r.status === 200 });
        errorRate.add(!ok);
    });

    if (PAYMENT_URL) {
        sleep(1);

        group('Payment initiation redirect', function () {
            const res = http.get(PAYMENT_URL, { redirects: 0 });
            const ok = check(res, { 'payment initiate redirects': (r) => r.status === 302 });
            errorRate.add(!ok);
        });
    }

    sleep(2);
}
