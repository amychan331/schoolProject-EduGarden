The idea of shopping cart came from my PHP Programming classes, which ask the student to select one of three type of projects types.

The idea of a garden ecommerce come from my own frustration with shopping at a gardening site that just plain sucks.

Class project wise, some of the basic expectations about project elements are:
* PHP MVC.
* Maintaining a session.
* Ability to log in as admin and as user with different user permissions.
* Ajax.


Some of the additional goals I have is:
* Find a way to deal with tax and shipping cost, which was one of the major frustration I had with my previous gardening shop experience.  
* Make it friendly for both desktop and mobile device.
* Avoid making a design disaster. Just cause it is a class project does not means I should throw all my design lessons to the backburner.
* Incorporate some web security techniques, particularly the ones I have been reading at OSWASP.
* If possibly, add in features that make it accessibility-friendly.

Here are some images of what the web app currently look like:
* To begin with, validation happens in both client and server. If JavaScript was turn off for some reason, validation will still occur:
![Client Side Validation](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/clientSideValidation.png)
![Server Side Validation](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/serverSideValidation.png)
* Here is the landing page for a successful regular user login:
![Regular User Login](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/regularLogin.png)
* For the admin, there is also an inventory panel. Note that "Inventory" link will only appear on navigation bar if user have admin privilege:
![Admin Login](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/adminLogin.png)
* On the official inventory page, in addition to the inventory panel, there are also input submission to add or delete inventory items:
![Admin Inventory](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/adminInventory.png)
* On the cart page, incremental button was used:
![Cart Page](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/cart.png)
* The website also have a search bar that uses Ajax. The navigation bar adjusts according to search result:
![Search Bar Working](https://github.com/amychan331/schoolProject-EduGarden/blob/master/image/sample/ajaxSearch.png)
