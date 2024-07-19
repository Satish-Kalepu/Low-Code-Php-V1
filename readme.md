PHP LowCode Maker - Alpha
================

Inspired by BackendLess, Xano, Bubble.io, Retool, DronaHQ, etc. Using this light weight LowCode Development library/module, You can create WebPages, APIs or Cloud functions. You can also give wings to your existing php projects.

Use Cases
---------
- A LowCode Engine Maker & Engine processor modules which can be deployed on multi tier environments.
- A module or a microsite which can provide A Lowcode development platform to any existing PHP Application.
- An easy way to develop and deploy ServerLessFunctions ( AWS Lambda or Azure or GCP )
- No-code automation - connect different apps and services and automate workflows

### Features
- APIs Creation ( Scrapi alternative )
- WebPages with ready to use components ( SquareSpace/Wix alternative )
- Internal Tables ( CMS or HeadLess CMS )
- External Database Connectivity  (Mysql, MongoDB, Redis, Cassandra, DynamoDB, FireBase, etc,  ) ( Work in Progress )
- Captcha, Thumbs, PDF etc components
- Ready to integrate AWS, Azure, GCP, AirTable, Notion, Slack etc (Work in Progress)

## Technologies
Apache 2.4,  PHP 8.2, MongoDB 5+

## Install 

```git clone https://github.com/Satish-Kalepu/Php-LowCode-Engine-V1```

### Docker Compose
```docker compose up ```

### Docker 
Application: 

```docker run -d --rm -p 8888:80 -v $(pwd):/var/www/html/ satishkalepu/amazon-apache-php82```

for windows use %{pwd}% instead of $(pwd)

Database:

```docker run -d --rm -p 27017:27017 -e MONGO_INITDB_ROOT_USERNAME=stage -e MONGO_INITDB_ROOT_PASSWORD=stage mongo```

### Lampp
git pull into desired htdocs folder 

curretly supported only for linux environment.

USAGE
-----
http://localhost:8888/apimaker  (where you can design apis)

on first run it redirects to: http://localhost:8888/apimaker/__install.php

http://localhost:8888/engine (where engine renders and serves)

on first run it redirects to: http://localhost:8888/engine/__install.php

Make sure, apimaker service is reachable to engine service. Use https endpoints if hosted on public platforms. 

It is recommended not to include apimaker module in your projects. Keep engine in any desired folders in your project. 
In this way, your app will be light weight. 
