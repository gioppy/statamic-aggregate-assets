## Installation

You can install the addon directly from the Statamic interface after installing it. You can also install it directly from composer with the command:

```composer require gioppy/statamic-aggregate-assets```

## How to use

After installing the addon, create a new disk in the `config/filesystems.php` file:

```
'aggregated' => [
    'driver' => 'local',
    'root' => public_path('aggregated'),
    'url' => '/aggregated',
    'visibility' => 'public',
]
```

You can customize the root directory according to your needs. The only constraint is that the disc visibility must be publicly visible.

To aggregate files use tags pair `{{ aggregate:css }}` and `{{ aggregate:js }}` in your `layout.antlers.html` file.

For better usage, use Aggregate in conjunction with `{{ yield }}`:

```
<!doctype html>
<html lang="{{ site:short_locale }}">
    <head>
        <meta charset="utf-8">
        {{ aggregate:css }}
        {{ yield:css }}
        {{ /aggregate:css }}
    </head>
    <body>
        {{ template_content }}

        {{ aggregate:js }}
        {{ yield:js }}
        {{ /aggregate:js }}
    </body>
</html>
```

In your blueprint .antlers.html file use as always:

```
...
{{ section:css }}
<link rel="stylesheet" href="/theme/styles/style1.css" />
<link rel="stylesheet" href="/theme/styles/style2.css" />
{{ /section:css }}

{{ section:js }}
<script src="/theme/scripts/script1.js"></script>
<script src="/theme/scripts/script2.js"></script>
{{ /section:js }}
```

Remember that the contents of the {{ aggregate }} tags will be replaced with a single CSS or JavaScript tag: insert only locally CSS and JavaScript files.

**Finally, remember that aggregation does not rewrite `url()` within CSS files.**
