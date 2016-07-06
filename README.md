# Getting Started
## Steps
1. [Register Your Application](#register-your-application)
2. [Install Plugin (Quick & Easy)](#install-plugin-quick--easy) or [Install Plugin (Recommended)](#install-plugin-recommended)
3. [Configure Plugin](#configure-plugin)


## Register Your Application

First, you will need to register your website as an Application on the App Console. You can find a link to the App Console at the following address: https://login.amazon.com.

#### Register Your Login with Amazon Application
1. In the App Console register a new application by clicking the Register new Application button. The Register Your Application form will appear.
    
    Note: You will be redirected to Seller Central, which handles application registration for Login with Amazon. If this is your first time using Seller Central, you will be asked to setup a Seller Central account.
    
    ![Screenshot 1](https://images-na.ssl-images-amazon.com/images/G/01/lwa/common/images/screenshots/blank_base_registration_mini.png)
2. In the application details page, add basic details about your product. These details will be used on your website and mobile apps (if applicable).
    - Name Shown to Users. This is the name displayed on the consent screen when the users agree to share the information with your application. This name applies to Android, iOS, and website versions of your application.
    - Description. A description of your application for Login with Amazon users.
    - Privacy Notice URL. The Privacy URL is the location of your company or application's privacy policy. It is also displayed on the consent screen. This link is displayed to users when they first login to your application (for example, http://www.example.com/privacy.html).
    - Logo Image File. This logo is displayed on the sign-in and consent screen when users log into your website or mobile app.     
    The logo will be automatically resized to 50 x150 pixels. The following formats are accepted: PNG, JPEG, GIF.
3. When you are finished, click Save to save your changes. Your sample registration should look similar to this:
    
    ![Screenshot 2](https://images-na.ssl-images-amazon.com/images/G/01/lwa/common/images/screenshots/zappos_base_registration_mini.png)

#### Add a Website to your Application
1. From the Application screen, click Web Settings. You will automatically be assigned values for Client ID and Client Secret. The client ID identifies your website, and the client secret is used in some circumstances to verify your website is authentic. The client secret, like a password, is confidential. To view the client secret, click Show Secret. You should take note of your Client ID as you'll need it again later on.
    
    ![Screenshot 3](https://images-na.ssl-images-amazon.com/images/G/01/lwa/common/images/screenshots/blank_website_registration_mini.png)

2. To add Allowed JavaScript Origins or Allowed Return URLs to your application, click Edit.
    
    ![Screenshot 4](https://images-na.ssl-images-amazon.com/images/G/01/lwa/common/images/screenshots/blank_website_registration_2_mini.png)
    
    * You should add your domain as an Allowed JavaScript Origin.
        
    * You should add the root of your WordPress install followed by */wp-login.php?amazonLogin=1* as your Allowed Return URL. For example, if your wordpress blog is installed at https://www.example.com/blog you'd enter: https://www.example.com/blog/wp-login.php?amazonLogin=1
        
    
3. Click Save

## Install Plugin (Recommended)
This approach suggests modifying files locally. It is recommended if any of the following apply to you:
 - You're a developer.
 - You test your site on your own computer before releasing changes.
 - You have a staging site.
 - You use git or some other form of versioning software to manage your WordPress codebase.

If none of these apply to you, we suggest [the Quick & Easy approach](#install-plugin-quick--easy).

If you have a custom process, these instructions may need to be modified slightly to fit your deployment process.

1. Create a backup.
2. [Download the plugin](https://github.com/amzn/login-with-amazon-wordpress/raw/master/build/loginwithamazon.zip).
3. Extract the zip file.
4. Copy the *loginwithamazon* directory to your *wp-content/plugins* directory.
5. Test as desired.
6. Upload the changes to your live site.

## Install Plugin (Quick & Easy)
It is important that you create a backup before installing any extensions, including this one.

1. [Download the plugin](https://github.com/amzn/login-with-amazon-wordpress/raw/master/build/loginwithamazon.zip).
2. Log in to your WordPress control panel.
3. In the side bar select Plugins: Add New
4. From the top of the page select Upload Plugin.
5. Upload the file you downloaded in step 1 to the form you have navigated to.

## Configure Plugin
1. Log in to your WordPress control panel.
2. In the side bar select Settings: Login With Amazon Settings
3. Enter your Client ID from when you registered your application with Amazon and submit the form.
4. Verify that the Login With Amazon button is showing on the login page.
