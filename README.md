Baïkal on OpenShift
===================

Baïkal is a lightweight CalDAV + CardDAV server based on PHP, SQLite/MySQL and SabreDAV (http://baikal-server.com).

This git repository will help you to get up and running quickly w/ a Baïkal installation
on [OpenShift](https://openshift.com).  The backend database is MySQL and the database name is the 
same as your application name.  You can name
your application whatever you want.

Running on OpenShift
--------------------

Create an account at http://openshift.redhat.com/ and install the client tools (run 'rhc setup' after installing it).

Create an application (you can call your application whatever you want) based on this quickstart:

    rhc app create baikal php-5 mysql-5 --from-code=https://github.com/fabianofranz/baikal-openshift-quickstart.git

That's it, you can now checkout your application at:

    http://baikal-$yournamespace.rhcloud.com
    
The Baïkal web administration console allows you to manage CalDAV + CardDAV users and other installation properties. The default username and password are *admin*/*admin* and the web console is available at:

    http://baikal-$yournamespace.rhcloud.com/admin
