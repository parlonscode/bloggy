#!/usr/bin/env bash

bash scripts/reset_db.sh
symfony console doctrine:fixtures:load --no-interaction
