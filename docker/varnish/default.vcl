vcl 4.0;

backend default {
    .host = "nginx";
    .port = "80";
    .max_connections = 100; # That's it
}

sub vcl_recv {
	unset req.http.x-cache;
}

sub vcl_hit {
	set req.http.x-cache = "hit";
}

sub vcl_miss {
	set req.http.x-cache = "miss";
}

sub vcl_pass {
	set req.http.x-cache = "pass";
}

sub vcl_pipe {
	set req.http.x-cache = "pipe uncacheable";
}

sub vcl_synth {
	set resp.http.x-cache = "synth synth";
}

sub vcl_deliver {

    unset resp.http.Age;
    unset resp.http.X-Varnish;
    unset resp.http.Via;
    unset resp.http.Pragma;

    unset resp.http.X-Powered-By;
    unset resp.http.Server;
    set resp.http.Server = "0nefeed";

	if (obj.uncacheable) {
		set req.http.x-cache = req.http.x-cache + " uncacheable" ;
	} else {
		set req.http.x-cache = req.http.x-cache + " cached" ;
	}
	# uncomment the following line to show the information in the response
	set resp.http.x-cache = req.http.x-cache;
}

sub vcl_recv {

    if (req.http.Host) {
        set req.http.Host = regsub(req.http.Host, ":[0-9]+", "");
    }

    unset req.http.proxy;

    if (req.method != "GET" &&
        req.method != "HEAD" &&
        req.method != "PUT" &&
        req.method != "POST" &&
        req.method != "TRACE" &&
        req.method != "OPTIONS" &&
        req.method != "PATCH" &&
        req.method != "DELETE") {
        /* Non-RFC2616 or CONNECT which is weird. */
        return (pipe);
    }

    if (req.http.Upgrade ~ "(?i)websocket") {
        return (pipe);
    }

    if (req.method != "GET" && req.method != "HEAD") {
        return (pass);
    }

    if (req.url ~ "(\?|&)(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=") {
        set req.url = regsuball(req.url, "&(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "");
        set req.url = regsuball(req.url, "\?(utm_source|utm_medium|utm_campaign|utm_content|gclid|cx|ie|cof|siteurl)=([A-z0-9_\-\.%25]+)", "?");
        set req.url = regsub(req.url, "\?&", "?");
        set req.url = regsub(req.url, "\?$", "");
    }

    # Strip hash, server doesn't need it.
    if (req.url ~ "\#") {
        set req.url = regsub(req.url, "\#.*$", "");
    }

    # Strip a trailing ? if it exists
    if (req.url ~ "\?$") {
        set req.url = regsub(req.url, "\?$", "");
    }

    # Remove a ";" prefix in the cookie if present
    set req.http.Cookie = regsuball(req.http.Cookie, "^;\s*", "");

    # Are there cookies left with only spaces or that are empty?
    if (req.http.cookie ~ "^\s*$") {
        unset req.http.cookie;
    }

    #if (req.url ~ "^[^?]*\.(jpeg|jpg|png|ico)(\?.*)?$") {
    #    unset req.http.Cookie;
    #    return (hash);
    #}

    # Send Surrogate-Capability headers to announce ESI support to backend
    set req.http.Surrogate-Capability = "key=ESI/1.0";

    if (req.http.Authorization) {
        return (pass);
    }

    return (hash);
}

sub vcl_backend_response {


    # Make this server more anonymous
    unset beresp.http.Via;
    unset beresp.http.X-Powered-By;
    unset beresp.http.X-Varnish;
    unset beresp.http.Age;
    unset beresp.http.Server;
    set beresp.http.Server = "0nefeed";
    #set beresp.http.X-Powered-By = "UA";

    if ( beresp.http.content-type ~ "^(text|application/x-javascript|application/javascript|application/json)") {
        set beresp.do_gzip = true;
    }


    if (bereq.uncacheable) {
        return (deliver);
    } else if (beresp.ttl <= 0s ||
      beresp.http.Set-Cookie ||
      beresp.http.Surrogate-control ~ "no-store" ||
      (!beresp.http.Surrogate-Control &&
        beresp.http.Cache-Control ~ "no-cache|no-store|private") ||
      beresp.http.Vary == "*") {
        # Mark as "Hit-For-Pass" for the next 2 minutes
        set beresp.ttl = 120s;
        set beresp.uncacheable = true;
    }
    return (deliver);
}