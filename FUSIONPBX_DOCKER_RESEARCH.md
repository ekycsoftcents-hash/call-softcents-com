# FusionPBX Docker research notes

## Official documentation finding

Source: [FusionPBX Quick Install](https://docs.fusionpbx.com/en/latest/getting_started/quick_install.html)

The official quick-install guide assumes a minimal Debian 12 system with SSH enabled. Its installation script installs FusionPBX, the FreeSWITCH release package and dependencies including iptables, Fail2ban, Nginx, PHP-FPM and PostgreSQL. The official guide does not present a first-party Docker Compose deployment as the default path. It also recommends using an FQDN and enabling Fail2ban rules for FreeSWITCH and authentication attacks.

## Deployment implication

The project can still be fully Dockerized by building a version-pinned custom FusionPBX/FreeSWITCH image or by running a Dockerized PBX stack on a dedicated PBX host. We should not claim that a community image is an official production image. The Laravel stack must connect to the PBX container over a private network/VPN, with Event Socket port 8021 restricted to the Laravel worker IP.

## Community Docker repository finding

Source: [capitalfuse/fusionpbx-docker](https://github.com/capitalfuse/fusionpbx-docker)

This public repository is a community project, not the official FusionPBX documentation. It builds separate custom images for a PHP-FPM/FusionPBX layer and a FreeSWITCH layer. The README shows Dockerfile-based builds and versioned image tags, with a FreeSWITCH image around multi-gigabyte size. This supports using a custom, pinned PBX image strategy, but the repository should be audited and maintained before production use.
