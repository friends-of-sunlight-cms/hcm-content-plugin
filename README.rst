HCM content
###########

Adds HCM for loading content from other pages and articles.

.. contents::

Requirements
************

- SunLight CMS 8

Usage
*****

For content wrapping it is possible to set a custom template in the plugin configuration, using placeholders (``%id%``, ``%slug%``, ``%perex%``, ``%picture%`` (for article), ``%content%``).

Page content
^^^^^^^^^^^^

::

  [hcm]page_content,id[/hcm]

  [hcm]page_content,12[/hcm] //example
  [hcm]page_content,12-13-14[/hcm] //example

- id - page id or multiple ids separated by hyphens


Article content
^^^^^^^^^^^^^^^

::

  [hcm]article_content,id[/hcm]

  [hcm]article_content,67[/hcm] //example

- id - article id


Installation
************

::

    Copy the folder 'plugins' and its contents to the root directory

or

::

    Installation via administration: 'Administration > Plugins > Upload new plugins'
