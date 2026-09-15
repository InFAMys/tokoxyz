# Toko XYZ — Deployment Guide

Laravel 13 e-commerce app. Deploy on a Docker host (Ubuntu VM) behind Nginx Proxy Manager, reached publicly through Cloudflare Tunnel (CGNAT-safe, outbound-only).

## Architecture

```
Browser → Cloudflare edge (SSL) → cloudflared (bare-metal on VM) → NPM (:8080) → app:8321 → php-fpm/nginx
                                                                                         → MySQL (db container)
```

- `app` container runs nginx + php-fpm + scheduler + queue via supervisor (single unit).
- `db` = mysql:8.4, persisted in named volume `dbdata`.
- App storage (uploads) persisted via bind mount `./storage/app/public`.
- Auto-refund scheduler (`orders:reconcile`) runs hourly via `schedule:work` — **required**, no cron needed.

## Prerequisites

- Docker + Compose on the target VM
- Nginx Proxy Manager running (external network `nginx-proxy-manager_default`)
- cloudflared installed on the VM (bare-metal, not docker)
- Real Midtrans live keys, SMTP, Klikresi keys for production

## Deploy

```bash
git clone <repo> ~/DockerApps/tokoxyz && cd ~/DockerApps/tokoxyz
cp .env.example .env && vim .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize storage:link
docker compose logs -f app
```

### `.env` (production values)

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tokoxyz.infmys.my.id
DB_HOST=db
DB_DATABASE=tokoxyz
DB_USERNAME=toko
DB_PASSWORD=<set>
DB_ROOT_PASSWORD=<set>
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SERVER_KEY=<live>
MIDTRANS_CLIENT_KEY=<live>
MAIL_MAILER=smtp
MAIL_HOST=<smtp>
MAIL_FROM_ADDRESS=<email>
KLIKRESI_KEY=<live>
KLIKRESI_ORIGIN=<live>
KLIKRESI_COURIER=<live>
```

## Nginx Proxy Manager

1. Add Proxy Host.
2. Domain: `tokoxyz.infmys.my.id`, scheme `http`, forward host `app`, forward port `8321`.
3. No internal SSL (Cloudflare edge terminates TLS).

## Cloudflare Tunnel

cloudflared runs on the VM (not in docker). Add ingress route:

```
tokoxyz.infmys.my.id -> http://127.0.0.1:8080
```

(NPM published on host port 8080.)

## Verify

```bash
docker compose ps
docker compose exec app php artisan schedule:list    # 0 * * * * orders:reconcile
docker compose exec app php artisan about            # confirms env + DB
docker compose logs -f app
```

Then run a live payment flow end-to-end.

## Troubleshooting

- **Midtrans payments fail / refunds stuck** — check live keys and that the Midtrans refund feature is activated on the account (QRIS API refunds return 412 until enabled). App flags failures via `refund_failed_at` instead of falsely marking refunded.
- **Images not loading** — ensure `storage:link` ran and the bind mount is in place.
- **Frontend not reflecting changes** — rebuild image (`docker compose up -d --build`) since assets are compiled at build time.
- **Can't reach app from NPM** — confirm app is on the external `nginx-proxy-manager_default` network and forward port `8321` matches `docker/nginx.conf`.

## Update / rebuild

```bash
cd ~/DockerApps/tokoxyz
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize
```
