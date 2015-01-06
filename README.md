Primo-Book-Cover-Carousel
=========================

This project generates a rotating carousel of book covers representing book titles currently "trending" in my library. "Trending" being loosely defined as books that were recently returned :)

<iframe src="http://digitalcollections.pugetsound.edu/jcarousel/trending_c.html" width="100%" height="400" NDC=TRUE>

This script is more "proof-of-concept" than production, and provides an example of how to use the Alma Analytics API against an existing report.

The various components required for this project include:

1. Ex Libris Alma, and Alma Analytics
2. An Alma Analytics report that contains a list of book titles you want to report against.
3. JCarousel, a github project (https://github.com/jsor/jcarousel)
4. Openlibrary.org (the source for the cover art)
5. The PHP script included in this project (trending_c.php, "trending" because my list is comprised of books trending in my library, and _c for "carousel").
6. A place to run the script. (I actually run the script on our CONTENTdm server).


Quick-and-dirty "how it works" (to be continued)
==============================

1. Generate a list of titles of interest in Alma Analytics

	In my case, I wrote a report to generate a list of "trending" titles in our library. That is, titles that were checked out and recently returned.
	
	The Analytics report I am using updates nightly, so the list of titles is constantly refreshed.

2. Use a script similar to the "trending_c.php" to generate the carousel code.

	My code does include comments, that hopefully provide some guidance as to what is happening where.

3. Publish the carousel on the web as desired.

	In my case, I currently "refresh" the carousel once a night, so that each day, a fresh set of covers is provided.

To see the code in action, you can check out:

	http://www.pugetsound.edu/academics/academic-resources/collins-memorial-library/new-resources/recently-read-collins/

Since our campus public web site uses a semi-proprietary CMS with certain content restrictions, I publish the carousel to the site using an iframe tag, like so:

<code>iframe src="http://digitalcollections.pugetsound.edu/jcarousel/trending_c.html" width="100%" height="400" NDC=TRUE</code>


Notes about the script:
==============================

- My example is in PHP, and I am definitely not a programmer. The script is more proof-of-concept than production, so keep that in mind as you read the code.

- The actual "carousel" feature in use was taken from another github project: https://github.com/jsor/jcarousel. So much credit goes to the author of that code.

- The book covers are taken from openlibrary.org using a simple call to their web site. I have no association with openlibrary.org, and  cannot speak to the restrictions on use of their content. But you can refer to their web site at: https://openlibrary.org/dev/docs/api/covers for information about acceptable use.
