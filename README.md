# weBoost SpeedTest

A lightweight speed test service forked from [LibreSpeed](https://github.com/librespeed/speedtest).

## Compatibility

All modern browsers are supported: IE11, latest Edge, latest Chrome, latest Firefox, latest Safari.
Works with mobile versions too.

## Features

* Download
* Upload
* Ping
* Jitter
* IP Address, ISP, distance from server (optional)
* Telemetry (optional)
* Results sharing (optional)
* Multiple Points of Test (optional)

![Screenrecording of a running Speedtest](https://speedtest.fdossena.com/mpot_v6.gif)

## Deploying Changes

Once you've contributed changes to either the master or staging branch, you must ssh into the droplet hosting the apache web server.
* For staging, run `ssh root@speedtest-staging.weboost.com`
* For master, run `ssh root@speedtest-<region>.weboost.com`. The current supported regions are east and west. You'll need to update both regions each time the master branch changes.

To update the server: 
* Pull the latest changes with `git pull`.
* Copy the updated files over to the web server with `cp -r <files> /var/www/html/`.
* * The only files that may **currently** need to be copied over are **favicon.ico, index.html, servers.php, speedtest.js, speedtest_worker.js, and the backend and results directories**.
* * You do not need to copy all of them, only the ones that changed.
* Restart it with `systemctl reload apache2`.

## Environment Variables

Environment variables live on the web server at `/etc/apache2/sites-enabled/000-default.conf`.
To create a new environment variable, simply add `SetEnv <NAME> <value>` to a new line in that file.
When you make changes to the environment variables, make sure you restart the server with `systemctl reload apache2`.

## Creating a New Server/Region

To create a new speedtest server, the first thing you'll need to do is create a new droplet from a snapshot of an existing droplet. These instructions focus on the web server itself, not the code.
* Go to the DigitalOcean project.
* Select a speedtest droplet (east or west)
* Go to **Backups & Snapshots**
* Click **Take a Snapshot**
* Click **Take Live Snapshot**. You do not need to change the name of the snapshot.
* For adding a new region, locate the new snapshot, click the three dots to the right, click **Add to a Region**, and select the desired region.
* In that same menu, click **Create Droplet**.
* Choose the desired region.
* Choose your desired droplet plan. The current production droplets use first basic, premium intel plan.
* Select all ssh keys.
* Give it a name, matching the conventions of the other droplets for consistency.
* Make sure you select the correct project
* Click **Create Droplet**

Now that the droplet is created, ssh into it and follow the next steps:
* Go to `/etc/apache2/sites-enabled/000-default.conf` and replace every mention of the previous region (e.g., "speedtest-east.weboost.com") to the new region, including the SPEEDTEST_PRIMARY_REGION environment variable.
* Generate ssl certificates for the new server.
* * Run `openssl genrsa -out speedtest-<region>.weboost.com.key 2048`.
* * Run `chmod 600 speedtest-<region>.weboost.com.key`.
* * Run `openssl req -new -key speedtest-<region>.weboost.com.key -out speedtest-<region>.weboost.com.csr`.
* * Fill out the following form:
* * * Countryname: `US`
* * * State: `Utah`
* * * Locality: `St George`
* * * Organization: `Weboost`
* * * Organizational Unit: `SpeedTest`
* * * Common Name: `speedtest-<region>.weboost.com`
* * * Leave the rest blank.
* Move the key into the private folder using `mv ~/speedtest-<region>.weboost.com.key /etc/ssl/private/`.
* Once the CSR files are processed by Digicert, get the .crt files to the web server's root folder (e.g., using scp).
* Run `mv ~/DigiCertCA.crt /etc/ssl/certs/`.
* Run `mv ~/speedtest-east_weboost_com.crt /etc/ssl/certs`.
* Restart the server with `systemctl reload apache21` and you're done!


