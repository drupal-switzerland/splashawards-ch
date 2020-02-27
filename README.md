## Dropsolid Rocketship Distribution

_curated by Dropsolid_

The intent of Dropsolid Rocketship is to make **digital business easy** by 
providing a framework and best practice examples based on real life situations 
from a company that has built and managed over 500 Drupal sites.

Dropsolid Rocketship is a distribution that enables small to mid-market 
business to profit from an optimal start when getting your feet wet in Drupal. 
It provides best practices from deployment, configuration and ways to extend 
it, proven by many cases that have implemented and resulted in this learning.

All Drupal 8 sites at Dropsolid have Rocketship at its core. Junior developers 
learn the basics with Rocketship, while senior developers have had their say 
in how an optimal development codebase looks like.

As it is our goal to make digital business easy, there is no better way than 
being transparent in how we achieve that. 

Note that the current READMEs are straight from the old private version of this
distribution, and were written with our own devs in mind. So some things may
not make sense. Feel free to create issues on 
[drupal.org](https://drupal.org/project/dropsolid_rocketship) when you find 
something that should be made clearer.


### Best installed with [Composer](https://getcomposer.org/download/):

To install the most recent beta release:
```
composer create-project dropsolid/rocketship:^8.6@beta PROJECTNAME --no-dev --no-interaction
```

To install the dev version:
```
composer create-project dropsolid/rocketship:8.6.x-dev PROJECTNAME --stability dev --no-interaction
```

------------------

- [Rocketship IP](#rocketship-ip)  
    - [Basics](#basics)  
    - [Features](#features)  
    - [Search API](#search-api)  
- [Before you start](#before-you-start)  
- [Installing a site](#installing-a-site)  
- [After the installation](#after-the-installation)  
- [Development & Site-building](#development--site-building)  
- [Theming](#theming)  
- [Deployment](#deployment)  
- [Infra Roadmap](#infra-roadmap)  

### Rocketship IP

##### Basics
This is the company-wide install profile for Dropsolid. It is based
around Paragraphs and the Page content type. With just those two you should 
be able to build a large array of various pages.

Even overview pages, or landingspages, are built using the Page content type. 
Thanks to the Overview paragraph, and the Overview field, any type of View, 
or custom render array can be exposed as an option to be selected. Note, 
however, that by default the Webadmin role does not have access to that field.

##### Features
On top of that, we also have premade "Features" (not to be confused with the 
contrib module Features).

These can have up to three levels of complexity.

* [feature]_core is the simplest implementation, with no or almost no custom 
logic and is not much more than a content type, already set up for you. SEO, 
translation capability, permissions, view modes. Everything is ready to go.

* [feature]_basic adds some extras, specific to their FAs. For most, this 
simply means migrating an overview page with a view already set up.

* [feature]_advanced adds even more functionaly, and is the highest level of 
premade features we have. It can add forms, facet filters, alterations to view modes, etc.

Always read the project's FA properly, and if the additions on top of the 
feature's standard FA diverge too much, it is best to start from a lower 
feature level than it is to try and delete and alter the things the higher 
levels have already set up. It will be faster in the long run.

##### Search API
Every "Feature", every content type, implements a "Search Index" view mode. 
This view mode is added to the default Content Index (by default a database 
backend) and contains every field that should be searchable. In addition to 
that, some general Content fields are added to the index upon site 
installation; published, title, etc. Some features add their own unique fields 
to this index. We expect the same to be done for any custom Content Types
you create during development.

Every View created by the Features also uses this index, so all Views in Rocketship are
by default "Search API Views". We expect the same, *within reason*, for any new Views you
create during development. And if you must implement some strange filter or even sort, think
"Can't I just make this a Facet instead?" and then do that so others can reuse it later and
so that you can mix and match with the normal Facets.

### Before you start

- [Before you start](readme/before-install) [markdown]

### Installing a site

- [How to install](readme/install) [markdown]

### After the installation

- [After the installation](readme/after-install) [markdown]

### Development & Site-building

- [Development](readme/development) [markdown]

### Theming

- [Development](readme/theming) [markdown]

### Deployment

- [Deployment](readme/deploying) [markdown]