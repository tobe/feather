##feather
feather is a php microblog - essentially, it's an "interface" that allows you to blog and modify your content dynamically via `ssh` or such.

###Requirements
PHP 5.4+
Unix is preferred.

###How does it work
feather basically reads data (.entry files) from a directory (folder) you specify in your main page (which is typically `index.php`).
Additionally, you can make the project read a specific entry (useful for static pages), a fixed number of entries or the whole folder.
I'll leave the pagination up to you to think about it and to develop it ;)

###How to set it up.
It's really simple - you can see how it works by opening up `index.php` in the repository.
First thing's first, you should define the `$pattern` variable. This is your "entry div" (The div that gets repeated for every entry, this usually contains the post title, date, author and the body.)


In the `$pattern` variable you can use special tags which are dynamic and different for each post. The ones you can use are:
* `{nice_title}` - The title of the post (.entry file) with the `_` replaced with a `&nbsp;`, first letter capitalized and the `.entry` extension removed.
* `{title}` - The raw title of the post. Filename, if you wish.
* `{date}` - Last modified date in the format `d.m.Y` of the post (entry/file).
* `{nice_time}` - Last modified time of the entry in the format `H:i:s`.
* `{time}` - Returns the unix timestamp of entry.
* `{hostname}` - Returns the hostname of the machine.
* `{user}` - Returns the user of the machine. You can use this in conjuction with `{hostname}` to produce something like `user@server_box0`.
* `{data}` - The body (data) of the entry itself.


**Note**: You can change the format of the time and the date by editing `feather.php:107` and `feather.php:108`.
Now that you've got that sorted, it's time load the contents. Place the following in your content div, this is usually under `<div id="container">` or the `wrapper` or even HTML5's `<section>`.


```php
<?php
try {
    $feather = new feather('your_entries_directory/', $pattern);
}catch(Exception $e) {
    echo 'Exception: ' . $e->getMessage();
}
?>
```

Edit the the `your_entries_directory/` to match your entries directory. In the repository this is `entries\\` because I was testing this on Windows and not nix.
This code would load ALL entries from the directory. If you wish to load a fixed number of entries, use such:

```php
$feather = new feather('your_entries_directory/', $pattern, 5);
# This would load 5 entries.
```

And if you'd like to load a static entry, just pass the filename as a string.

```php
$feather = new feather('your_entries_directory/', $pattern, 'about_me.entry');
# This would load about_me.entry from (...)/your_entries_directory/about_me.entry
```

The path is absolute(?), which means `your_entries_directory/` gets translated to something like:
`/home/infy/www/htdocs/your_entries_directory/`


To write entries, just created `.entry` files in your entries directory and that's it. I use `vim` to edit them. Make sure they are readable and make sure to use underscores if you'd like `{nice_title}`  to translate spaces properly!
You can also use BBCode in your entries, since raw HTML is disabled by default. If you would like to enable RAW HTML input, do the following:

1. Remove lines 60 and 61.
2. Add the following on line 60: `$content['data'] = $data;`

You can use the following BBCode tags:

* `[bold][/bold]` - Makes your text strong (weight 700)
* `[italic][/italic]` - Makes your text `<em>`
* `[underline][/underline]` - Makes your text get underlined.
* `[strikethrough][/strikethrough]` - ..really?
* `[image][/image]` - `<a href="$1" target="_blank"><img src="..." class="featherimg" style="max-width: 750px; max-height: 400px;"></a>`
* `[link][/link]` - Makes your text a hyperlink.
* `[font color="#H3XC0L0R"][/font]` - Colorizes your font.
* `[heading=1][/heading]` - Makes your text `<h1>`. Change the `1` to some other number for other sizes.
* `[center][/center]` - Centres your content.
* `[list][/list]` - `<li></li>`.
* `[ul][/ul]` - `<ul></ul>`.
* `[code][/code]` - `<pre></pre>`.
* `[codeline][/codeline]` - `<code></code>`.
* `[codebox][/codebox]` - Fancy `<pre>` codebox.
* `[p][/p]` - PARAGRAPH.

**IF you would like to see these more in detail, go to line 83-98!**

**Note:** If you are running this on a non posix compliant environment such as Windows, you should really edit the line 10 if you plan on using the `{user}` variable.
That should be it. Hf.