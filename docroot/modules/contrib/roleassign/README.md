# Role Assign

RoleAssign specifically allows site administrators to further delegate
the task of managing user's roles.

RoleAssign introduces a new permission called 'assign roles'. Users
with this permission are able to assign selected roles to still other
users. Only users with the 'administer permissions' permission may
select which roles are available for assignment through this module.

This module was developped by TBarregren and is now maintained by salvis.
Drupal 7 port by salvis.
Drupal 8 port by svendecabooter and tkuldeep17.


## Table of contents

- Background
- Requirements
- Installation
- Configuration
- Usage
- Beware
- Maintainers


## Background

It is possible for site administrators to delegate the user
administration through the 'administer users' permission. But that
doesn't include the right to assign roles to users. That is necessary if
the delegatee should be able to administrate user accounts without
intervention from a site administrator.

To delegate the assignment of roles, site administrators have had until
now no other choice than also grant the 'administer permissions'
permission. But that is not advisable, since it gives right to access
all roles, and worse, to grant any rights to any role. That can be
abused by the delegatee, who can assign himself all rights and thereby
take control over the site.

This module solves this dilemma by introducing the 'assign roles'
permission. While editing a user's account information, a user with this
permission will be able to select roles for the user from a set of
available roles. Roles available are configured by users with the
'administer permissions' permission.


## Requirements

This module requires no modules outside of Drupal core.


## Installation

1. Copy the entire 'roleassign' directory, containing the
   'roleassign.module' and other files, to your Drupal modules directory.
2. Log in as site administrator.
3. Go to the administration page for modules and enable the module.


## Configuration

1. Log in as site administrator.
2. Go to the Permissions page (people/permissions) and grant the 'assign roles'
   permission to those roles that should be able to assign roles to other users.
   Notice that besides the 'assign roles' permission, these roles also must have
   the 'administer users' permission.
3. Go to the administration page for RoleAssign (people/permissions/roleassign)
   and select those roles that should be available for assignment by users with
   'assign roles' permission.
4. For each user that should be able to assign roles, go to the user's account
   and select a role with both the 'assign roles' and the 'administer users'
   permissions.

Beware: Granting the 'administer users' permission to users will allow them to
modify admin passwords or email addresses. The User Protect module can help to
prevent this. RoleAssign will protect user 1's name, email, and password fields,
but it won't protect any other accounts.


## Usage

1. Log in as a user with both the 'assign roles' and the 'administer users'
   permissions.
2. To change the roles of a user, go to the user's account and review the
   assignable roles and change them as necessary.


## Beware

'Administer users' is and remains a security-critical permission that must NOT
be given to untrusted users! RoleAssign will keep your assistant admins within
their limits, but if you introduce alternative ways to edit users, assign roles,
or give permissions (like the "Administration: Users" view in the popular
Administration Views module), then you may be opening up ways for your
assistant admins to gain additional privileges.

## Maintainers

- Ankit Babbar - [webankit](https://www.drupal.org/u/dave-reid)
- Hans Salvisberg - [salvis](https://www.drupal.org/u/salvis)
- Jordan Thompson - [nord102](https://www.drupal.org/u/dave-reid)
- joseph.olstad - [josepholstad](https://www.drupal.org/u/josepholstad)
- Sven Decabooter - [svendecabooter](https://www.drupal.org/u/smk-ka)
- Thomas Barregren - [TBarregren](https://www.drupal.org/u/dave-reid)
- Tobias Sjösten - [tobiassjosten](https://www.drupal.org/u/dave-reid)
