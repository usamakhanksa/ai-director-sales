# API curl reference (Prisma / Next.js App Router backend)

Base URL in dev: `http://localhost:3000` (Next picks the next free port if
3000 is busy — check your terminal output). **Note:** this project has
`trailingSlash: true` in `next.config.js`, so every path below needs the
trailing `/` or you'll get a 308 redirect instead of a JSON response.

Seeded accounts (from `prisma/seed.ts`):
| Email | Password | Role |
|---|---|---|
| admin@scrapegenius.com | AdminPass123! | ADMIN |
| alice@example.com | Password123! | USER |
| bob@example.com | Password123! | USER |

Seeded purchase code ready to activate: **`12345`**

## Auth

```bash
# Signup
curl -X POST http://localhost:3000/api/auth/signup/ \
  -H "Content-Type: application/json" \
  -d '{"name":"Jane Doe","email":"jane@example.com","password":"Password123!"}'

# Login -> returns { data: { token, user } }
curl -X POST http://localhost:3000/api/auth/login/ \
  -H "Content-Type: application/json" \
  -d '{"email":"alice@example.com","password":"Password123!"}'
```

Save the token for the authenticated calls below:

```bash
TOKEN=$(curl -s -X POST http://localhost:3000/api/auth/login/ \
  -H "Content-Type: application/json" \
  -d '{"email":"alice@example.com","password":"Password123!"}' | jq -r .data.token)
```

## API keys & usage (per-user Google Custom Search key pool)

```bash
# List this user's active, non-exhausted keys
curl http://localhost:3000/api/get_keys/ \
  -H "Authorization: Bearer $TOKEN"

# Atomically increment usage for a key (fails with 429 once daily_limit is hit)
curl -X POST http://localhost:3000/api/update_usage/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"api_key_id":1,"increment_by":1}'
```

## Saving scraped data + dashboard stats

```bash
# Persist a batch of scraped results and bump the matching dashboard tile
curl -X POST http://localhost:3000/api/saved/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "query": "seo agencies dubai",
    "source": "GOOGLE",
    "stat_type": "Emails Scraped",
    "data": [{"link":"a.com"},{"link":"b.com"},{"link":"c.com"}]
  }'

# Fetch aggregated dashboard stats -> [{ title, records }, ...]
curl http://localhost:3000/api/dashboard/stats/ \
  -H "Authorization: Bearer $TOKEN"
```

## Purchase code activation

```bash
# Activate the seeded "12345" code on the logged-in account
curl -X POST http://localhost:3000/api/purchase-code/activate/ \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"code":"12345"}'
```

Responses you should expect:
- First call from any account → `"Purchase code activated"`
- Same account calls again → `"Purchase code already active on this account"`
- A *different* account tries the same code → 409 `"This purchase code has already been claimed by another account"`
- Unknown code → 404 `"Invalid purchase code"`
- Past `expiresAt` → 410 `"This purchase code has expired"`

## Error shape

Every route returns the same envelope:

```json
{ "success": true, "data": { ... } }
{ "success": false, "error": "message" }
```

401 for missing/invalid/expired JWT, 400 for zod validation failures (message
lists every failing field), 404/409/410/429 for the specific conditions
above, 500 for anything unexpected (logged server-side, never leaked to the
client).
