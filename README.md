Primo-Book-Cover-Carousel
=========================

Rotating carousel of book covers representing book titles pulled from an Alma Analytics report.

This script is more "proof-of-concept" than production, and provides an example of how to use the Alma Analytics API against an existing report.

Quick-and-dirty "how it works"
==============================

1. Generate a list of titles of interest in Alma Analytics

	In my case, I wrote a report to generate a list of "trending" titles in our library. That is, titles that were checked out and recently returned.
	
	The Analytics report I am using updates nightly, so the list of titles is constantly refreshed.

2. Use a script similar to the "trending_c.php" to generate the carousel code.

3. Publish the carousel on the web as desired.

	In my case, I currently "refresh" the carrousel once a night, so that each day, a fresh set of covers is provided.

To see the code in action, I have a carousel running at:

http://www.pugetsound.edu/academics/academic-resources/collins-memorial-library/new-resources/recently-read-collins/



Notes about the script:
==============================

- My example is in PHP, and I am definitely *not* a programmer. The script is more proof-of-concept than production, so keep that in mind as you read the code.

-The actual "carousel" feature in use was taken from another github project: https://github.com/jsor/jcarousel. So much credit goes to the author of that code.

-The book covers are taken from openlibrary.org using a simple call to their web site. I have no association with openlibrary.org, and so cannot speak to the restrictions on use of their content. But you can refer to their web site at: https://openlibrary.org/dev/docs/api/covers for information about acceptable use.
