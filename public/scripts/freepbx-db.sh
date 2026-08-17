#!/bin/bash

set -e

CONFIG="/etc/mysql/mariadb.conf.d/50-server.cnf"
DBNAME="asteriskcdrdb"

echo "===== MariaDB Remote Access Setup ====="

# Root check
if [[ $EUID -ne 0 ]]; then
    echo "Please run as root."
    exit 1
fi

# Config check
if [[ ! -f "$CONFIG" ]]; then
    echo "Config not found: $CONFIG"
    exit 1
fi

echo
echo "Backing up config..."
cp "$CONFIG" "${CONFIG}.bak.$(date +%F-%H%M%S)"

echo
echo "Updating bind-address..."

sed -i \
's/^[[:space:]]*bind-address[[:space:]]*=.*/bind-address = 0.0.0.0/' \
"$CONFIG"

echo "bind-address updated."

echo
echo "Restarting MariaDB..."
systemctl restart mariadb

echo
echo "Checking MariaDB status..."

if systemctl is-active --quiet mariadb; then
    echo "MariaDB is running."
else
    echo "MariaDB failed to start."
    systemctl status mariadb --no-pager
    exit 1
fi

echo
echo "Verifying 3306 listening..."

ss -tulnp | grep 3306 || {
    echo "Port 3306 is not listening."
    exit 1
}

echo
echo "===== Database User Setup ====="

read -rp "Database username: " DBUSER
read -rp "Password: " DBPASS
read -rp "Allowed IP(s) (comma separated): " ALLOWEDIPS

IFS=',' read -ra IPS <<< "$ALLOWEDIPS"

echo
echo "Creating user(s)..."

for ip in "${IPS[@]}"
do
    ip=$(echo "$ip" | xargs)

    mysql -u root <<EOF
CREATE USER IF NOT EXISTS '$DBUSER'@'$ip'
IDENTIFIED BY '$DBPASS';

GRANT ALL PRIVILEGES
ON ${DBNAME}.*
TO '$DBUSER'@'$ip';

FLUSH PRIVILEGES;
EOF

    echo "✓ Created: $DBUSER@$ip"
done

echo
echo "===== Verification ====="

mysql -u root -e "
SELECT User,Host
FROM mysql.user
WHERE User='${DBUSER}';
"

echo
echo "Done."
echo "Database: ${DBNAME}"
echo "Firewall rules not configured yet."