<?php

/*
 * Plugin Name: WP-FormAssembly
 * Plugin URI: http://www.formassembly.com/plugins/wordpress/
 * Description: Embed a FormAssembly Web Form in a WordPress Post or Page. To use, add a [formassembly formid=NNNN] tag to your post. To create your web form, go to https://www.formassembly.com
 * Version: 3.0.2
 * Author: FormAssembly / Drew Buschhorn
 * Author URI: https://www.formassembly.com
 */

/*
 * Inspired by: http://www.satollo.com/english/wordpress/include-it/
 */

/*
 * Basic Usage:
 *
 * [formassembly formid=NNNN]
 * or
 * [formassembly workflowid=NNNN]
 *
 * (where NNNN is the ID of a form or workflow created with FormAssembly)
 *
 * Advanced Attributes:
 *  iframe="true"         Render as iframe
 *  style="XXX: YYYY;"    Add CSS overrides to either Form or Iframe
 *  shortname="xxx"       For non-APP-hosted forms, prepends shortname to .tfaforms.net
 *  server="a URL"        Backward compatibility – if set and matches *.tfaforms.net, its shortname is extracted.
 */


add_shortcode('formassembly', 'fa_add');
add_filter('the_content', 'fa_handle');

function fa_handle($content)
{
    $open_b = '[[';
    $x = strpos($content, $open_b . "formassembly");
    if ($x === false) {
        return $content;
    }

    $warningMessage = "<!-- Old style formassembly [[XXXXX XXXXX]] tag replaced -->\n";
    return preg_replace('/\[\[formassembly (.*)\]\]/U', $warningMessage . '[formassembly $1]', $content);
}

function fa_add($atts)
{
    $qs = isset($_SERVER['QUERY_STRING']) ? wp_sanitize_redirect($_SERVER['QUERY_STRING']) : '';
    if (!empty($qs)) {
        $qs = '?' . $qs;
    };

    $host_url = resolve_host_url($atts);
    $new_content = '';

    if (isset($atts['formid']) || isset($atts['workflowid'])) {

        $action_url = "forms/view";
        $fa_id = absint($atts['formid']);

        if (isset($atts['workflowid'])) {
            $action_url = "workflows/start";
            $fa_id = absint($atts['workflowid']);
        }

        // Add style options in to combat wordpresses' default centering of forms.
        if (!isset($atts['style'])) {
            $style = "<style>.wForm form{text-align: left;}</style>";
        } else {
            $style = "<style>.wForm form{" . htmlspecialchars($atts['style'], ENT_QUOTES) . "}</style>";
        }

        if (isset($atts['iframe'])) {
            // IFRAME method

            /**
             * Add jsid to maintain session in browsers that block cookies.
             * Setting as null informs the server we are in an iframe without
             * an active session.
             */
            $qs .= (strpos($qs, '?') !== false ? '&' : '?') . 'jsid=';
            $url = $host_url . '/' . $action_url . '/' . $fa_id . $qs;

            // validate url
            if (!wp_http_validate_url($url)) {
                return $style . "<div style=\"color:red;margin-left:auto;margin-right:auto;\">Invalid url added to server attribute to your FormAssembly tag.</div>";
            }

            if (!isset($atts['style'])) {
                $atts['style'] = "width: 100%; min-height: 650px;";
            }
            $attributes = implode(' ', array("frameborder=0", "style='" . htmlspecialchars($atts['style'], ENT_QUOTES) . "'"));
            $new_content = '<iframe ' . $attributes . ' src="' . $url . '"></iframe>';
        } else {
            // REST API method

            if (
                isset($_GET['tfa_next']) &&
                isset($atts['formid']) &&
                isTfaNextInvalid($_GET['tfa_next'], $fa_id)
            ) {
                return $style . "<div style=\"color:red;margin-left:auto;margin-right:auto;\">Invalid url provided in tfa_next parameter</div>";
            }

            if (
                isset($_GET['tfa_next']) &&
                isset($atts['workflowid']) &&
                isTfaNextInvalidForWorkflowId($_GET['tfa_next'], $fa_id)
            ) {
                return $style . "<div style=\"color:red;margin-left:auto;margin-right:auto;\">Invalid url provided in tfa_next parameter</div>";
            }

            if (!isset($_GET['tfa_next'])) {
                $url = $host_url . '/rest/' . $action_url . '/' . $fa_id . $qs;
            } else {
                $url = $host_url . '/rest' . wp_sanitize_redirect($_GET['tfa_next']);
            }

            //validate url
            if (!wp_http_validate_url($url)) {
                return $style . "<div style=\"color:red;margin-left:auto;margin-right:auto;\">Invalid url added to server attribute to your FormAssembly tag.</div>";
            }

            if (function_exists("wp_remote_get")) {
                $response = wp_remote_get($url);
                $responseCode = wp_remote_retrieve_response_code($response);
                $responseBody = wp_remote_retrieve_body($response);

                if ($responseCode != 200) {
                    return $style . "<div style=\"color:red;margin-left:auto;margin-right:auto;\">" . $responseCode . '<br>' . $responseBody . "</div>";
                }
                $buffer = $responseBody;
            } else {
                // REST API call not supported, must use iframe instead.
                $buffer = "<div style=\"color:red;margin-left:auto;margin-right:auto;\">Your server does not support this form publishing method. Try adding iframe=\"1\" to your FormAssembly tag.</div>";
            }

            $new_content = $style . $buffer;
        }
    }

    return $new_content;
}

function isTfaNextInvalid($tfaNext, $faID)
{
    if (
        preg_match('/^\/responses\/last_success.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/responses\/last_error.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/responses\/saved\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/view\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/legacyView\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/review\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/resume\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/reset_password\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/forms\/help\/' . $faID . '.*$/m', $tfaNext) === 0 &&
        preg_match('/^\/wf\/[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+(\?.*)?$/', $tfaNext) === 0
    ) {
        return true;
    }

    return false;
}

function isTfaNextInvalidForWorkflowId($tfaNext, $workflowId)
{
    if (
        (
            preg_match('/^\/responses\/last_success.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/responses\/last_error.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/responses\/saved.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/view.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/legacyView.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/review.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/resume.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/reset_password.*$/m', $tfaNext) === 0 &&
            preg_match('/^\/forms\/help.*$/m', $tfaNext) === 0
        ) ||
        (
            (
                preg_match('/^\/forms\/view.*$/m', $tfaNext) === 1 ||
                preg_match('/^\/forms\/legacyView.*$/m', $tfaNext) === 1 ||
                preg_match('/^\/forms\/review.*$/m', $tfaNext) === 1 ||
                preg_match('/^\/forms\/resume.*$/m', $tfaNext) === 1 ||
                preg_match('/^\/forms\/reset_password.*$/m', $tfaNext) === 1 ||
                preg_match('/^\/forms\/help.*$/m', $tfaNext) === 1
            ) &&
            strpos($tfaNext, sprintf('tfa_dbWorkflowId=%s', $workflowId)) === false
        )
    ) {
        return true;
    }

    return false;
}

function resolve_host_url($atts)
{
    if (!empty($atts['shortname'])) {
        $shortname = preg_replace('/[^a-zA-Z0-9.\-]/', '', $atts['shortname']);
        if (substr($shortname, -4) === '.gov') {
            $shortname = substr($shortname, 0, -4);
            return "https://{$shortname}.govfa.net";
        }

        return "https://{$shortname}.tfaforms.net";
    }

    if (isset($atts['server']) && wp_http_validate_url($atts['server']) !== false) {
        $parsed_url = parse_url($atts['server']);
        if (!empty($parsed_url['host'])) {
            if (preg_match('/^(.*)\.tfaforms\.net$/i', $parsed_url['host'], $matches)) {
                return "https://{$matches[1]}.tfaforms.net";
            }
            if (preg_match('/^(.*)\.govfa\.net$/i', $parsed_url['host'], $matches)) {
                return "https://{$matches[1]}.govfa.net";
            }
        }
    }

    return "https://app.formassembly.com";
}
