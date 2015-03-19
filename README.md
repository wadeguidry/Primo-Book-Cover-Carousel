Primo-Book-Cover-Carousel
=========================

This project generates a rotating carousel of book covers representing book titles currently "trending" in my library. In this case, "trending" is loosely defined as books that were recently checked out and returned :)

I am publishing this project primarily for members of the Orbis Cascade Alliance, but other Ex Libris Alma customers may also find this project of interest.

To see the code in action, you can check out:

http://www.pugetsound.edu/academics/academic-resources/collins-memorial-library/new-resources/recently-read-collins/

This script is more "proof-of-concept" than production, and provides an example of how to use the Alma Analytics API.

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
	
	I have placed a copy of my actual analytics report in the Alma Analytics community folder, at:
	
	<b>/Shared Folders/Community/Reports/Shared Reports/Reports/Fulfillment – Misc. Reports/Univ Puget Sound – trending_titles</b>
	
	It is helpful to consider the actual report along with the script, because the script is written specifically to read the results of this report (ie, report field order and field names as returned by the API are important)
	
	You can tweak the contents of your own analytics report as desired, maybe books that have been checked out twice recently. 

2. Use a script similar to the "trending_c.php" to generate the carousel code.

	My code does include comments, that hopefully provide some guidance as to what is happening where.

3. Publish the carousel on the web as desired.

	In my case, I currently "refresh" the carousel once a night, so that each day, a fresh set of covers is provided.
	
	I run the script at a scheduled time each night, using a scheduled task on a Windows 2008 server, using the trending.bat file included in the repository.

To see the code in action, you can check out:

http://www.pugetsound.edu/academics/academic-resources/collins-memorial-library/new-resources/recently-read-collins/

Since our campus public web site uses a semi-proprietary CMS with certain content restrictions, I publish the carousel to the site using an iframe tag, like so:

<code>iframe src="http://digitalcollections.pugetsound.edu/jcarousel/trending_c.html" width="100%" height="400" NDC=TRUE</code>


Other notes and considerations:
==============================

- My example is in PHP, and I am definitely not a programmer. The script is more proof-of-concept than production, so keep that in mind as you read the code.

- The actual "carousel" feature in use was taken from another github project: https://github.com/jsor/jcarousel. So much credit goes to the author of that code.

- The book covers are taken from openlibrary.org using a simple call to their web site. I have no association with openlibrary.org, and  cannot speak to the restrictions on use of their content. But you can refer to their web site at: https://openlibrary.org/dev/docs/api/covers for information about acceptable use.

- The toughest part of the Alma Analytics API for me was understanding how to deal with XML namespace used in the data returned by the API. Thanks goes to Kate Deibel at University of Washington library for helping me with that part.

- Since overdue books stay "attached" to the patron record until the fines are paid, I only include books in the report if they were returned on time (ie, not late). I don't want to include books if there is even a remote chance that the title could be traced back to a specific library patron.
