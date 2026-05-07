# Q8 Bootcamp Labs — Server Infrastructure

The static OSINT pages for Day 1 (`sarah-linkedin.html`, `ahmed-linkedin.html`, `yousef-linkedin.html`, `q8-logistics.html`, `portal.html`) are served free from GitHub Pages — no VPS needed.

For Days 4–9 we need a real Linux box with vulnerable services running. ~$5/month covers everything.

---

## Step 1 — Get a VPS (5 min)

[**Hetzner Cloud**](https://www.hetzner.com/cloud) — fastest, cheapest, EU-based.

1. Sign up at hetzner.com/cloud
2. Create a new project: `Q8 Bootcamp`
3. **Add Server** with these settings:
   - **Location:** Helsinki or Nuremberg (closest EU regions to Kuwait)
   - **Image:** Ubuntu 24.04
   - **Type:** **CX22** — €4.51/mo, 2 vCPU, 4 GB RAM, 40 GB disk (handles 20+ concurrent students)
   - **SSH key:** paste your public key (run `cat ~/.ssh/id_ed25519.pub` on your laptop; if you don't have one yet, run `ssh-keygen -t ed25519` first)
   - **Name:** `q8-bootcamp-lab`
4. Click Create. Note the public IP that appears (e.g., `123.45.67.89`).

**Alternatives if you don't want Hetzner:** DigitalOcean ($6/mo), Linode ($5/mo), AWS Lightsail ($5/mo). Same idea, same commands below.

---

## Step 2 — First Login

```bash
ssh root@<YOUR_VPS_IP>
```

If it asks about authenticity, type `yes`. You're in.

---

## Step 3 — Setup (5 min, one-time)

Paste this entire block into the SSH session:

```bash
# Update OS
apt update && apt upgrade -y

# Install Docker + Compose + git + firewall
apt install -y docker.io docker-compose-v2 git ufw

# Enable Docker
systemctl enable --now docker

# Firewall — only the ports our labs need
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp     # SSH (you, the instructor)
ufw allow 80/tcp     # HTTP — Day 4 portal & Day 6 file upload
ufw allow 21/tcp     # FTP — Day 5 vsftpd
ufw allow 6200/tcp   # vsftpd backdoor port — Day 5
ufw allow 2222/tcp   # SSH for students — Day 8 (non-default to keep noise off port 22)
ufw --force enable

# Pull the labs repo
cd /opt
git clone https://github.com/hancoded/q8-bootcamp-labs.git
cd q8-bootcamp-labs/labs

echo "✓ Setup complete. You are at: $(pwd)"
echo "✓ VPS IP for students: $(curl -s ifconfig.me)"
```

---

## Step 4 — Deploy a Lab

Each day's lab is one folder. To run a lab, `cd` into that folder and `docker compose up -d`.

```bash
cd /opt/q8-bootcamp-labs/labs/day4-sqli
docker compose up -d

# Wait ~10 seconds for the database to initialize, then verify:
curl http://localhost
# Should return HTML for the Q8 portal homepage
```

Give your students the VPS IP. They point Burp/sqlmap/curl at `http://<VPS_IP>` and start the lab.

---

## Day-by-Day Status

| Day | Folder | Ports | Status |
|---|---|---|---|
| 4 — SQL Injection | `day4-sqli/` | 80 | ✅ Ready |
| 5 — Metasploit / vsftpd | `day5-metasploit/` | 21, 6200 | 🚧 In progress |
| 6 — File Upload Webshells | `day6-fileupload/` | 80 | 🚧 In progress |
| 8 — Privesc + Pivot | `day8-privesc/` | 2222 | 🚧 In progress |

Days 1, 2, 3, 7, 9 don't need server-side infrastructure — they use the OSINT pages, students' own machines (Nmap/networking labs), or local files (hash cracking).

---

## Common Operations

### Watch what's happening

```bash
docker compose logs -f          # tail logs
docker compose ps               # see what's running
```

### Reset a lab (students broke it / random bot owned it)

```bash
cd /opt/q8-bootcamp-labs/labs/day4-sqli
docker compose down -v          # -v wipes the database too — fresh slate
docker compose up -d --build
```

### Stop everything between bootcamps to save $

```bash
docker compose down
# Or shut the whole VPS down from Hetzner console — pay only when running
```

### Pull updates to lab code

```bash
cd /opt/q8-bootcamp-labs
git pull
cd labs/day4-sqli
docker compose up -d --build    # rebuild with new code
```

---

## Security Notes

- **The vulnerabilities here are intentional.** Random internet bots will scan/attack this box. That's fine — all data is fictional, the whole stack rebuilds in 30 seconds.
- **Don't put real data on this box.** Don't reuse passwords from your real systems. Don't link real services.
- **Tear down between bootcamps.** Shut the box off when not in use.
- The `auth.log` filling up with brute-force attempts is actually a **teaching moment** — show students on day 1 what a real internet-facing host gets.

---

## Costs

| Item | Cost |
|---|---|
| Hetzner CX22 (always-on) | €4.51/month |
| Hetzner CX22 (only during 2-week bootcamp) | ~€2.50 |
| Docker images / disk | included |
| Bandwidth | included (20 TB/month) |

You can add a backup snapshot before each bootcamp for €0.45/month — recovers the full state if something breaks.
