<?php
/**
 * author		Pagayo
 * web			www.pagayo.com
 * license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Theme		pt002
 */
?>
<?php
$this->startSetup();
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$staticBlock = array(
                'title' => 'Follow us',
                'identifier' => 'pt002-footer-social',                   
                'content' => '<ul class="social-icons">
                	<li><a href="#" title="facebook"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-facebook.png"}}" alt="facebook"/></a></li>
                	<li><a href="#" title="twitter"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-twitter.png"}}" alt="twitter"/></a></li>
                	<li><a href="#" title="googleplus"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-googleplus.png"}}" alt="googleplus"/></a></li>
                	<li><a href="#" title="youtube"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-youtube.png"}}" alt="youtube"/></a></li>
                	<li><a href="#" title="vimeo"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-vimeo.png"}}" alt="vimeo"/></a></li>
                	<li><a href="#" title="pinterest"><img src="{{media url="wysiwyg/pagayo/pt002/icon-social-pinterest.png"}}" alt="pinterest"/></a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Customer Service',
                'identifier' => 'pt002-footer-box1',                   
                'content' => '<ul>
                	<li><a href="{{store url="customer-service"}}">Customer Service</a></li>
                	<li><a href="{{store url="sales/guest/form"}}">Orders and Returns</a></li>
                	<li><a href="{{store url="customer-service"}}">Shipping and Delivery</a></li>
                	<li><a href="{{store url="catalog/seo_sitemap/category"}}">Sitemap</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'About Us',
                'identifier' => 'pt002-footer-box2',                   
                'content' => '<ul>
                	<li><a href="{{store url="about-magento-demo-store"}}">About Us</a></li>
                	<li><a href="{{store url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy</a></li>
                	<li><a href="{{store url="contacts"}}">Contact Us</a></li>
                	<li><a href="{{store url="customer-service"}}">Shopping Infos</a></li>	
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'New Products',
                'identifier' => 'pt002-footer-box3',                   
                'content' => '<ul>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Popular',
                'identifier' => 'pt002-footer-box4',                   
                'content' => '<ul>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                	<li><a href="{{store url="#"}}">Textlink</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'My Account',
                'identifier' => 'pt002-footer-box5',                   
                'content' => '<ul>
                	<li><a href="{{store url="customer/account"}}">My Account</a></li>
                	<li><a href="{{store url="wishlist"}}">My Wishlist</a></li>
                	<li><a href="{{store url="checkout/cart"}}">View Cart</a></li>
                	<li><a href="{{store url="checkout"}}">Checkout</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Footer Links',
                'identifier' => 'pt002-footer-links',                   
                'content' => '<ul class="links">
                	<li class="first"><a href="{{store url="about-magento-demo-store"}}">About Us</a></li>
                	<li><a href="{{store url="customer-service"}}">Customer Service</a></li>
                	<li><a href="{{store url="contacts"}}">Contact Us</a></li>
                	<li><a href="{{store url="catalogsearch/advanced"}}">Advanced Search</a></li>
                	<li><a href="{{store url="catalogsearch/term/popular"}}">Search Terms</a></li>
                	<li><a href="{{store url="catalog/seo_sitemap/category"}}">Sitemap</a></li>
                	<li><a class="link-rss" href="{{store url="rss"}}">RSS</a></li>
                	<li><a href="{{store url="sales/guest/form"}}">Orders and Returns</a></li>
                	<li class="last privacy"><a href="{{store url="privacy-policy-cookie-restriction-mode"}}">Privacy Policy</a></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Footer Payments',
                'identifier' => 'pt002-footer-payments',                   
                'content' => '<ul class="payment-icons">
                	<li><img src="{{media url="wysiwyg/pagayo/pt002/icon-payment-paypal.png"}}" alt="PayPal"/></li>
                	<li><img src="{{media url="wysiwyg/pagayo/pt002/icon-payment-visa.png"}}" alt="Visa"/></li>
                	<li><img src="{{media url="wysiwyg/pagayo/pt002/icon-payment-mastercard.png"}}" alt="MasterCard"/></li>
                	<li><img src="{{media url="wysiwyg/pagayo/pt002/icon-payment-amex.png"}}" alt="Amercian Express"/></li>
                	<li><img src="{{media url="wysiwyg/pagayo/pt002/icon-payment-moneybookers.png"}}" alt="Moneybookers"/></li>
                </ul>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Fast Shipping',
                'identifier' => 'pt002-header-usp1',                   
                'content' => 'and easy returns',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Secure Shopping',
                'identifier' => 'pt002-header-usp2',                   
                'content' => 'with fast checkout',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'Call Center',
                'identifier' => 'pt002-header-usp3',                   
                'content' => '0123-456789',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();
               
$staticBlock = array(
                'title' => 'pt002-home-box1',
                'identifier' => 'pt002-home-box1',                   
                'content' => '<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-home-box1.jpg"}}"/></a>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'pt002-home-box2',
                'identifier' => 'pt002-home-box2',                   
                'content' => '<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-home-box2.jpg"}}"/></a>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'pt002-home-box3',
                'identifier' => 'pt002-home-box3',                   
                'content' => '<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-home-box3.jpg"}}"/></a>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'pt002-home-box4',
                'identifier' => 'pt002-home-box4',                   
                'content' => '<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-home-box4.jpg"}}"/></a>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();
                  
$staticBlock = array(
                'title' => 'pt002-banner-left',
                'identifier' => 'pt002-banner-left',                   
                'content' => '<div class="block block-banner-left">
                	<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-banner-left.png"}}"/></a>
                </div>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$staticBlock = array(
                'title' => 'pt002-banner-right',
                'identifier' => 'pt002-banner-right',                   
                'content' => '<div class="block block-banner-right">
                	<a href="{{store url="#"}}"><img src="{{media url="wysiwyg/pagayo/pt002/pt002-banner-right.png"}}"/></a>
                </div>',
                'is_active' => 1,                   
                'stores' => array(0)
                );
Mage::getModel('cms/block')->setData($staticBlock)->save();

$this->endSetup();
?>