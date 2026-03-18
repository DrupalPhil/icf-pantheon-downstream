# GitHub Workflows

This repository includes GitHub Actions workflows that automate deployments to Pantheon.

## Workflows Overview

### Caller Workflows

| Workflow | File | Trigger | Description |
|---|---|---|---|
| **Deploy main** | `deploy-main.yml` | Push to `main` | Deploys the `main` branch to the Pantheon **dev** environment. Cleans up stale multidev environments. |
| **Deploy SIPA Branch** | `deploy-multidev.yml` | Push to any `SIPA-*` branch | Deploys SIPA ticket branches to a Pantheon multidev named `sipa-<number>` (e.g. `sipa-123`). |
| **Cleanup Multidev** | `cleanup-multidev.yml` | Deletion of any `SIPA-*` branch | Automatically deletes the corresponding Pantheon multidev when a `SIPA-*` branch is deleted. |

### Reusable Workflows

| Workflow | File | Description |
|---|---|---|
| **Deploy to Pantheon** | `_deploy.yml` | Checks out the repo, installs Composer dependencies (production-only), and pushes the build artifact to Pantheon using the `push-to-pantheon` action. |
| **Post-deploy Drush tasks** | `_post-deploy.yml` | Runs `drush updatedb`, `drush config:import`, and `drush cache:rebuild` on the target Pantheon environment via Terminus. |

## Required Secrets

These must be configured in **Settings → Secrets and variables → Actions → Secrets** for the repository (or organization).

| Secret | Description |
|---|---|
| `PANTHEON_SSH_KEY` | A private SSH key whose public counterpart is registered in the Pantheon dashboard. Used to push code and run remote Drush commands. |
| `PANTHEON_MACHINE_TOKEN` | A Pantheon machine token used to authenticate Terminus CLI commands (e.g. multidev management, Drush). Generate one at **Pantheon Dashboard → Account → Machine Tokens**. |

## Required Variables

These must be configured in **Settings → Secrets and variables → Actions → Variables** for the repository (or organization).

| Variable | Description |
|---|---|
| `PANTHEON_SITE_NAME` | The Pantheon site machine name (e.g. `my-drupal-site`). Used to target the correct site in Terminus and the deploy action. |
| `IS_UPSTREAM` | Set to `true` **only** on the upstream/template repository to prevent workflows from running. On downstream sites that use this upstream, leave unset or set to any value other than `true`. |

## How It Works

```
push to main ──► deploy-main.yml ──► _deploy.yml (target: dev)
                                  └─► _post-deploy.yml (env: dev)

push to SIPA-* ──► deploy-multidev.yml ──► _deploy.yml (target: sipa-<number>)
                                        └─► _post-deploy.yml (env: sipa-<number>)

SIPA-* branch deleted ──► cleanup-multidev.yml ──► deletes multidev sipa-<number>
```

### Deployment Flow

1. **Build** – The `_deploy.yml` workflow checks out the code, sets up PHP 8.3, and runs `composer install --no-dev` to produce a production-ready artifact.
2. **Push** – The artifact is pushed to Pantheon via the `push-to-pantheon` action using the SSH key and machine token.
3. **Post-deploy** – The `_post-deploy.yml` workflow connects to the Pantheon environment via Terminus and runs database updates, config import, and cache rebuild.

### Multidev Naming

- **SIPA branch-based**: The `SIPA-###` portion of the branch name is extracted and lowercased to produce the multidev name (e.g. branch `SIPA-123-my-feature` → multidev `sipa-123`). Anything after the ticket number is ignored.

### Concurrency

All workflows use concurrency groups to prevent parallel deploys to the same environment. In-progress deploys are **not** cancelled — newer runs queue behind the current one.
