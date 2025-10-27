#!/usr/bin/env bash

USER="$1"
PASSWD="$2"

( echo "${PASSWD}"; echo "${PASSWD}" ) | smbpasswd  -U "${USER}"

