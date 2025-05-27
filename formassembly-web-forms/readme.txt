=== WP-FormAssembly ===
Contributors: FormAssembly / Drew Buschhorn
Tags: forms
Requires at least: 4.0.0
Tested up to: 6.7
Stable tag: 3.0.2

Quickly embed FormAssembly web forms with the FormAssembly WordPress Plugin! Create contact forms, applications, payment forms, & surveys.

== Description ==

Quickly embed FormAssembly web forms into your website with the FormAssembly WordPress Plugin!

Use FormAssembly's all-in-one form builder and data collection platform to:
Create contact forms, applications, payment forms, and surveys. Integrate data with Salesforce, PayPal, Google Apps and more. Keep data secure with GDPR/CCPA compliance, PCI DSS Level 1 Certification, and encryption at rest.

Sign up for a free trial at FormAssembly.com/sign-up to start building your form, then use the plugin and shortcode to embed forms onto your WordPress site.

== Installation ==

Shortcodes

Replace 123456 with your form ID. Learn more: https://help.formassembly.com/knowledgebase/articles/340363-wordpress

Example shortcode for Basic, Professional, & Premier plans:
[formassembly formid=123456]

Shortcode for Essentials, Team or Enterprise plans:
[formassembly formid=123456 shortname=”organization”]

Shortcode for Government plans:
[formassembly formid=123456 shortname=”organization.gov”]

To use this plugin, you will need a FormAssembly account.

= Example Shortcodes =

FormAssembly Basic, Professional, & Premier plans – (tfaforms.com):
[formassembly formid=123456]

FormAssembly Essentials, Team or Enterprise plans – (organization.tfaforms.net):
[formassembly formid=123456 shortname=”organization”]

Government plans – (organization.govfa.net):
[formassembly formid=123456 shortname=”organization.gov”]

Publish with an iframe - (Inline Frame):

If you'd rather display the form in an inline frame, or if your server doesn't support the default publishing method, add the iframe attribute to your tag.

For instance, a benefit to using an iframe would be to avoid conflicting CSS rules between the parent site and the embedded form.
[formassembly formid=123456 iframe=1]

OR

[formassembly formid=123456 shortname="organization" iframe=1]

Publish a Legacy Workflow:

[formassembly workflowid=1234]

Add Style:

It is possible to add CSS to your shortcode to control the size of the form/iframe in the page. For example, you can define the width:
[formassembly formid=123456 iframe=1 style="width: 300px !important;"]

https://help.formassembly.com/knowledgebase/articles/340363-wordpress

1. Upload wp_formassembly.zip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place [formassembly formid=NNNN] shortcode in your post.

== Changelog ==
= 3.0.1 =
*   Update documentation
= 3.0.0 =
*   Security improvements
*   Dropped support/use of custom domain in server shortcode
*   Introduced shortname shortcode 
*   Bump 'Tested up to' version
= 2.0.11 =
*   XSS security fix
= 2.0.10 =
*   Security improvements
= 2.0.9 =
*   Security improvements
= 2.0.8 =
*   Bump 'Tested up to' version
*   Fix plugin version
*   Use WP HTTP API instead of cURL
*   Security improvements
= 2.0.7 =
*   Bump 'Tested up to' version
= 2.0.6 =
*   Security Updates
= 2.0.5 =
*   Support iframed forms in Safari.
= 2.0.4 =
*   Update formatting and tested compatibility version.
= 2.0.3 =
*   Update contributor name.
= 2.0.2 =
*   Defaults to https, not http.
= 2.0.1 =
*   Updated plugin description with single bracket shortcode example.
= 2.0 =
*   Updated release for WordPress shortcodes.
*   Uses single bracket, but try to keep backwards compatibility with double bracket notation for now.
= 1.0 =
*   Initial release.
*   Uses double bracket notation.
