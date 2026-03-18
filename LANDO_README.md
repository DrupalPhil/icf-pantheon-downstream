# Lando Local Development

This project uses [Lando](https://lando.dev/) with the [Pantheon recipe](https://docs.lando.dev/plugins/pantheon/) for local development.

---

## Getting Started

### 1. Install Prerequisites

- [Lando](https://docs.lando.dev/install/) (v3.21+ recommended)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### 2. Create Your Local Lando Config

`.lando.yml` is **gitignored** — each developer maintains their own copy. Start by copying the template:

```bash
cp .lando.example.yml .lando.yml
```

### 3. Customize `.lando.yml`

Edit the following values for your specific project:

| Field    | Description                                      | Where to Find It                          |
|----------|--------------------------------------------------|-------------------------------------------|
| `name`   | Local project name (becomes `<name>.lndo.site`)  | Choose a unique name for your project     |
| `id`     | Pantheon site UUID                                | Pantheon Dashboard → Settings → About     |
| `site`   | Pantheon machine name                             | Pantheon Dashboard URL slug               |

> **Important:** The `name` value appears in **two places** — the top-level `name:` field and the `tooling.drush.cmd` URI (`--uri=https://YOUR-PROJECT-NAME.lndo.site`). Make sure both match your chosen project name. This ensures commands like `lando drush uli` generate correct URLs instead of `//default`.

### 4. Start Lando

```bash
lando start
```

On first start, Lando will pull your database and files from Pantheon (if `id` and `site` are configured).

---

## Common Lando Commands

### Lifecycle

| Command           | Description                                      |
|-------------------|--------------------------------------------------|
| `lando start`     | Start the local environment                      |
| `lando stop`      | Stop the local environment                       |
| `lando restart`   | Restart the local environment                    |
| `lando rebuild`   | Rebuild the environment (use after config changes)|
| `lando destroy`   | Destroy the local environment completely         |
| `lando info`      | Show connection info (URLs, DB creds, etc.)      |

### Drush (Drupal CLI)

| Command                          | Description                                  |
|----------------------------------|----------------------------------------------|
| `lando drush cr`                 | Clear/rebuild Drupal caches                  |
| `lando drush uli`                | Generate a one-time admin login link         |
| `lando drush updb`               | Run pending database updates                 |
| `lando drush cim -y`             | Import configuration from `config/` directory|
| `lando drush cex -y`             | Export configuration to `config/` directory   |
| `lando drush st`                 | Show Drupal status report                    |
| `lando drush sql-cli`            | Open an interactive MySQL shell              |
| `lando drush watchdog:show`      | View recent log messages                     |

### Composer

| Command                              | Description                              |
|--------------------------------------|------------------------------------------|
| `lando composer install`             | Install dependencies from `composer.lock`|
| `lando composer require drupal/module_name` | Add a new Drupal module            |
| `lando composer update`              | Update all dependencies                  |

### Pantheon Integration

| Command                                         | Description                                          |
|-------------------------------------------------|------------------------------------------------------|
| `lando pull`                                    | Pull database, files, and/or code from Pantheon      |
| `lando push`                                    | Push database, files, and/or code to Pantheon        |
| `lando terminus auth:login`                     | Authenticate with Pantheon via Terminus               |
| `lando terminus env:info`                       | Show info about a Pantheon environment                |
| `lando terminus env:wake`                       | Wake a sleeping Pantheon environment                  |
| `lando terminus drush <site>.<env> -- <command>` | Run Drush on a remote Pantheon environment |

### Database

| Command                              | Description                                      |
|--------------------------------------|--------------------------------------------------|
| `lando db-import <file>.sql`         | Import a SQL dump into the local database        |
| `lando db-export`                    | Export the local database to a SQL file           |

### Tooling & Debugging

| Command                              | Description                                      |
|--------------------------------------|--------------------------------------------------|
| `lando php -v`                       | Check PHP version                                |
| `lando mysql`                        | Open a MySQL shell                               |
| `lando ssh`                          | SSH into the appserver container                 |
| `lando xdebug-on`                   | Enable Xdebug (if configured)                    |
| `lando xdebug-off`                  | Disable Xdebug                                   |
| `lando logs`                         | View container logs                              |

---

## Typical Workflow

```bash
# 1. Pull latest from Pantheon
lando pull --database=dev --files=dev --code=none

# 2. Run database updates
lando drush updb -y

# 3. Import config
lando drush cim -y

# 4. Clear caches
lando drush cr

# 5. Generate a login link
lando drush uli
```

---

## phpMyAdmin

This project includes phpMyAdmin for visual database management. After `lando start`, find the phpMyAdmin URL by running:

```bash
lando info
```

Look for the `phpmyadmin` service URLs in the output.

---

## Troubleshooting

| Issue                                | Solution                                         |
|--------------------------------------|--------------------------------------------------|
| Port conflicts                       | Make sure no other Lando projects share the same `name` |
| Stale containers                     | Run `lando rebuild` to rebuild from scratch      |
| Authentication errors with Pantheon  | Run `lando terminus auth:login` and follow prompts|
| Xdebug not working                   | Ensure `xdebug: true` is set in `.lando.yml` and your IDE is listening |
| Out of disk space                    | Run `docker system prune` to clean up unused images/containers |

---

## Notes

- **Never commit `.lando.yml`** — it is gitignored. All shared Lando configuration changes should go in `.lando.example.yml`.
- The `id` and `site` fields in `.lando.yml` connect your local environment to a specific Pantheon site. Without them, `lando pull` and `lando push` will not work.
- Xdebug is enabled by default in the example config. Set `xdebug: false` if you don't need it (improves performance).
