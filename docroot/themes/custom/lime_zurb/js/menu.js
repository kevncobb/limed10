(function (Drupal, $, window) {
/////////////// begin menu widget definition /////////////////////
//
// Function Menubar() is the constructor of a menu widget
// The widget will bind to the ul passed to it.
//
// @param(id string) id is the HTML id of the ul to bind to
//
// @param(vmenu boolean) vmenu is true if menu is vertical; false if horizontal
//
// @return N/A
//
    var Menubar = function (id, vmenu) {

        // define widget properties
        this.$id = $('#' + id);

        this.$rootItems = this.$id.children('ul.root-level li[tabindex="0"]'); // jQuery array of all root-level menu items
        this.$items = this.$id.find('.menu-item').not('.separator'); // jQuery array of menu items

        this.$parents = this.$id.find('.menu-item--expanded'); // jQuery array of menu items
        this.$parents.attr( "aria-haspopup", "true" );
        this.$allItems = this.$parents.add(this.$items); // jQuery array of all menu items

        //console.log('All items:');
        //console.log(this.$allItems);
        //console.log('Parents: ');
        //console.log(this.$parents);


        this.$activeItem = null; // jQuery object of the menu item with focus

        this.vmenu = vmenu;
        this.bChildOpen = false; // true if child menu is open

        this.keys = {
            tab:    9,
            enter:  13,
            esc:    27,
            space:  32,
            left:   37,
            up:     38,
            right:  39,
            down:   40
        };

        // bind event handlers
        this.bindHandlers();

        // associate the menu with the textArea it controls
        // this.textarea = new textArea(this.$id.attr('aria-controls'));
    };

//
// Function bindHandlers() is a member function to bind event handlers for the widget.
//
// @return N/A
//
    Menubar.prototype.bindHandlers = function() {

        var thisObj = this;

        ///////// bind mouse event handlers //////////

        // bind a handler for the menu items
        this.$items.mouseenter(function(e) {
            $(this).addClass('menu-hover');
            return true;
        });

        // bind a mouseout handler for the menu items
        this.$items.mouseout(function(e) {
            $(this).removeClass('menu-hover');
            return true;
        });

        // bind a mouseenter handler for the menu parents
        this.$parents.mouseenter(function(e) {
            return thisObj.handleMouseEnter($(this), e);
        });

        // bind a mouseleave handler
        this.$parents.mouseleave(function(e) {
            return thisObj.handleMouseLeave($(this), e);
        });

        // bind a click handler
        this.$allItems.click(function(e) {
            return thisObj.handleClick($(this), e);
        });

        //////////// bind key event handlers //////////////////

        // bind a keydown handler
        this.$allItems.keydown(function(e) {
            return thisObj.handleKeyDown($(this), e);
        });

        // bind a keypress handler
        this.$allItems.keypress(function(e) {
            return thisObj.handleKeyPress($(this), e);
        });

        // bind a focus handler
        this.$allItems.focus(function(e) {
            return thisObj.handleFocus($(this), e);
        });

        // bind a blur handler
        this.$allItems.blur(function(e) {
            return thisObj.handleBlur($(this), e);
        });

        // bind a document click handler
        $(document).click(function(e) {
            return thisObj.handleDocumentClick(e);
        });

    } // end bindHandlers()

//
// Function handleMouseEnter() is a member function to process mouseover
// events for the top menus.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false;
//
    Menubar.prototype.handleMouseEnter = function($item, e) {

        // add hover style
        $item.addClass('menu-hover');
        // expand the first level submenu
        if ($item.hasClass ('menu-item--expanded')) {
            $item.children('ul').attr('aria-hidden', 'false');
            this.bChildOpen = true;
        }
        //e.stopPropagation();
        return true;

    } // end handleMouseEnter()

//
// Function handleMouseOut() is a member function to process mouseout
// events for the top menus.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false;
//
    Menubar.prototype.handleMouseOut = function($item, e) {

        // Remover hover styles
        $item.removeClass('menu-hover');

        //e.stopPropagation();
        return true;

    } // end handleMouseOut()

//
// Function handleMouseLeave() is a member function to process mouseout
// events for the top menus.
//
// @param($menu object) $menu is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false;
//
    Menubar.prototype.handleMouseLeave = function($menu, e) {

        var $active = $menu.find('.menu-focus'); //???

        $active = $active.add($menu.find('.menu-focus'));

        // Remove hover style
        $menu.removeClass('menu-hover');

        // if any item in the child menu has focus, move focus to the root item
        if ($active.length > 0) {

            this.bChildOpen = false;

            // remove the focus style from the active item
            $active.removeClass('menu-focus');

            // store the active item
            this.$activeItem = $menu;

            // cannot hide items with focus -- move focus to root item
            $menu.focus();
        }

        // hide the child menu
        $menu.children('ul').attr('aria-hidden', 'true');

        //e.stopPropagation();
        return true;

    } // end handleMouseLeave()

//
// Function handleClick() is a member function to process click events
// for the top menus.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false;
//
    Menubar.prototype.handleClick = function($item, e) {

        var $parentUL = $item.parent();

        if ($parentUL.is('.root-level')) {
            // open the child menu if it is closed
            $item.children('ul').first().attr('aria-hidden', 'false');
            this.bChildOpen = true;
        }
        else {
            // remove hover and focus styling
            this.$allItems.removeClass('menu-hover menu-focus');

            // close the menu
            this.$id.find('ul').not('.root-level').attr('aria-hidden','true');
        }

        // if menu item triggers some behavior other than going to a link,
        // would stop propagation and return false
        // e.stopPropagation();
        // return false;
        return true;

    } // end handleClick()

//
// Function handleFocus() is a member function to process focus events
// for the menu.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns true;
//
    Menubar.prototype.handleFocus = function($item, e) {

        // if activeItem is null, we are getting focus from outside the menu. Store
        // the item that triggered the event
        if (this.$activeItem == null) {
            this.$activeItem = $item;
        }
        else if ($item[0] != this.$activeItem[0]) {
            return true;
        }

        // get the set of jquery objects for all the parent items of the active item
        var $parentItems = this.$activeItem.parentsUntil('nav').filter('li');

        // remove focus styling from all other menu items
        this.$allItems.removeClass('menu-focus');

        // add styling to the active item
        this.$activeItem.addClass('menu-focus');
        /*
         if (this.$activeItem.hasClass('menu-item--expanded')) {
         // for parent items, add .menu-focus directly to the list item
         this.$activeItem.addClass('menu-focus');
         }
         else {
         // for sub-menu items, add .menu-focus to the anchor
         this.$activeItem.find('a').addClass('menu-focus');
         }
         */
        // add styling to all parent items........ Why would you do this?
        // $parentItems.addClass('menu-focus');

        if (this.vmenu == true) {
            // if the bChildOpen is true, open the active item's child menu (if applicable)
            if (this.bChildOpen == true) {

                var $itemUL = $item.parent();

                // if the itemUL is a root-level menu and item is a parent item,
                // show the child menu.
                if ($itemUL.is('.root-level') && ($item.attr('aria-haspopup') == 'true')) {
                    $item.children('ul').attr('aria-hidden', 'false');
                }
            }
            else {
                this.vmenu = false;
            }
        }

        return true;

    } // end handleFocus()

//
// Function handleBlur() is a member function to process blur events
// for the menu.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns true;
//
    Menubar.prototype.handleBlur = function($item, e) {

        // $item.find('a').removeClass('menu-focus');
        $item.removeClass('menu-focus');

        return true;

    } // end handleBlur()

//
// Function handleKeyDown() is a member function to process keydown events
// for the menus.
//
// @param($item object) $item is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false if consuming; true if propagating
//
    Menubar.prototype.handleKeyDown = function($item, e) {

        if (e.altKey || e.ctrlKey) {
            // Modifier key pressed: Do not process
            return true;
        }

        switch(e.keyCode) {
            case this.keys.tab: {

                // hide all menu items and update their aria attributes
                this.$id.find('ul').attr('aria-hidden', 'true');

                // remove focus styling from all menu items
                this.$allItems.removeClass('menu-focus');

                this.$activeItem = null;
                this.bChildOpen == false;

                break;
            }
            case this.keys.esc: {

                var $itemUL = $item.parent();

                if ($itemUL.is('.root-level')) {
                    // hide the child menu and update the aria attributes
                    $item.children('ul').first().attr('aria-hidden', 'true');
                }
                else {

                    // move up one level
                    this.$activeItem = $itemUL.parent();

                    // reset the childOpen flag
                    this.bChildOpen = false;

                    // set focus on the new item
                    this.$activeItem.focus();

                    // hide the active menu and update the aria attributes
                    $itemUL.attr('aria-hidden', 'true');
                }

                e.stopPropagation();
                return false;
            }
            case this.keys.enter:
            case this.keys.space: {

                if (!($item.hasClass('menu-item--expanded'))) {
                    // user pressed enter or space on a dropdown menu item,
                    // not an item on the menu bar
                    // get the target href and go there
                    window.location = $item.find('a').attr('href');
                    console.log($item.find('a').attr('href'));
                    return false;
                }
                if ($item.hasClass('menu-item--expanded')) {
                    // user pressed enter or space on a dropdown menu item,
                    // not an item on the menu bar
                    // get the target href and go there
                    window.location = $item.find('a').attr('href');
                    console.log($item.find('a').attr('href'));
                    return false;
                }

                var $parentUL = $item.parent();

                if ($parentUL.is('.root-level')) {
                    // open the child menu if it is closed
                    $item.children('ul').first().attr('aria-hidden', 'false');
                    this.bChildOpen = true;
                }
                else {
                    // remove hover styling
                    this.$allItems.removeClass('menu-hover');
                    this.$allItems.removeClass('menu-focus');

                    // close the menu
                    this.$id.find('ul').not('.root-level').attr('aria-hidden','true');

                    // clear the active item
                    this.$activeItem = null;

                }
                e.stopPropagation();
                return false;
            }

            case this.keys.left: {

                if (this.vmenu == true && $itemUL.is('.root-level')) {
                    // If this is a vertical menu and the root-level is active, move
                    // to the previous item in the menu
                    this.$activeItem = this.moveUp($item);
                }
                else {
                    this.$activeItem = this.moveToPrevious($item);
                }

                this.$activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.right: {

                if (this.vmenu == true && $itemUL.is('.root-level')) {
                    // If this is a vertical menu and the root-level is active, move
                    // to the next item in the menu
                    this.$activeItem = this.moveDown($item);
                }
                else {
                    this.$activeItem = this.moveToNext($item);
                }

                this.$activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.up: {

                if (this.vmenu == true && $itemUL.is('.root-level')) {
                    // If this is a vertical menu and the root-level is active, move
                    // to the previous root-level menu
                    this.$activeItem = this.moveToPrevious($item);
                }
                else {
                    this.$activeItem = this.moveUp($item);
                }

                this.$activeItem.focus();

                e.stopPropagation();
                return false;
            }
            case this.keys.down: {

                if (this.vmenu == true && $itemUL.is('.root-level')) {
                    // If this is a vertical menu and the root-level is active, move
                    // to the next root-level menu
                    this.$activeItem = this.moveToNext($item);
                }
                else {
                    this.$activeItem = this.moveDown($item);
                }

                this.$activeItem.focus();

                e.stopPropagation();
                return false;
            }
        } // end switch
        return true;

    } // end handleKeyDown()

//
// Function moveToNext() is a member function to move to the next menu level.
// This will be either the next root-level menu or the child of a menu parent. If
// at the root level and the active item is the last in the menu, this function will loop
// to the first menu item.
//
// If the menu is a horizontal menu, the first child element of the newly selected menu will
// be selected
//
// @param($item object) $item is the active menu item
//
// @return (object) Returns the item to move to. Returns $item is no move is possible
//
    Menubar.prototype.moveToNext = function($item) {

        var $itemUL = $item.parent(); // $item's containing menu
        var $menuItems = $itemUL.children('li'); // the items in the currently active menu
        var menuNum = $menuItems.length; // the number of items in the active menu
        var menuIndex = $menuItems.index($item); // the items index in its menu
        var $parentLI = $itemUL.parent();

        var $newItem = null;
        var $newItemUL = null;

        if ($itemUL.is('.root-level')) {
            // this is the root level move to next sibling. This will require closing
            // the current child menu and opening the new one.

            if (menuIndex < menuNum-1) { // not the last root menu
                $newItem = $item.next();
            }
            else { // wrap to first item
                $newItem = $menuItems.first();
            }

            // close the current child menu (if applicable)
            if ($item.attr('aria-haspopup') == 'true') {

                var $childMenu = $item.children('ul').first();

                if ($childMenu.attr('aria-hidden') == 'false') {
                    // hide the child and update aria attributes accordingly
                    $childMenu.attr('aria-hidden', 'true');
                    this.bChildOpen = true;
                }
            }

            // remove the focus styling from the current menu
            $item.removeClass('menu-focus');

        }
        return $newItem;
    }

//
// Function moveToPrevious() is a member function to move to the previous menu level.
// This will be either the previous root-level menu or the child of a menu parent. If
// at the root level and the active item is the first in the menu, this function will loop
// to the last menu item.
//
// If the menu is a horizontal menu, the first child element of the newly selected menu will
// be selected
//
// @param($item object) $item is the active menu item
//
// @return (object) Returns the item to move to. Returns $item is no move is possible
//
    Menubar.prototype.moveToPrevious = function($item) {

        var $itemUL = $item.parent(); // $item's containing menu
        var $menuItems = $itemUL.children('li'); // the items in the currently active menu
        var menuNum = $menuItems.length; // the number of items in the active menu
        var menuIndex = $menuItems.index($item); // the items index in its menu
        var $newItem = null;
        var $newItemUL = null;
        var $parentLI = $itemUL.parent();

        if ($itemUL.is('.root-level')) {
            // this is the root level move to previous sibling. This will require closing
            // the current child menu and opening the new one.

            if (menuIndex > 0) { // not the first root menu
                $newItem = $item.prev();
                console.log('executing item.prev');
            }
            else { // wrap to last item
                $newItem = $menuItems.last();
                console.log('executing menuItems.last');
            }

            // close the current child menu (if applicable)
            if ($item.attr('aria-haspopup') == 'true') {

                var $childMenu = $item.children('ul').first();

                if ($childMenu.attr('aria-hidden') == 'false') {
                    // hide the child and update aria attributes accordingly
                    $childMenu.attr('aria-hidden', 'true');
                    this.bChildOpen = true;
                }
            }

            // remove the focus styling from the current menu
            $item.removeClass('menu-focus');
            /*
            // open the new child menu (if applicable)
            if (($newItem.attr('aria-haspopup') == 'true') && this.bChildOpen == true) {

                var $childMenu = $newItem.children('ul').first();

                // open the child and update aria attributes accordingly
                $childMenu.attr('aria-hidden', 'false');

                if (!this.vmenu) {
                    // select the first item in the child menu
                    $newItem = $childMenu.children('li').first();
                }
            }
            */
        }
        return $newItem;
    }

//
// Function moveDown() is a member function to select the next item in a menu.
// If the active item is the last in the menu, this function will loop to the
// first menu item.
//
// @param($item object) $item is the active menu item
//
// @param(startChr char) [optional] startChr is the character to attempt to match against the beginning of the
// menu item titles. If found, focus moves to the next menu item beginning with that character.
//
// @return (object) Returns the item to move to. Returns $item is no move is possible
//
    Menubar.prototype.moveDown = function($item, startChr) {

        var $itemUL = $item.parent(); // $item's containing menu
        var $menuItems = $itemUL.children('li').not('.separator'); // the items in the currently active menu
        var menuNum = $menuItems.length; // the number of items in the active menu
        var menuIndex = $menuItems.index($item); // the items index in its menu
        var $newItem = null;
        var $newItemUL = null;

        if ($itemUL.is('.root-level')) { // this is the root level menu

            if ($item.attr('aria-haspopup') != 'true') {
                // No child menu to move to
                return $item;
            }
            // remove the focus styling from the current item
                    //console.log($itemUL.find('li.menu-focus'));
            $itemUL.find('li.menu-focus').removeClass('menu-focus');
                    //console.log($itemUL.find('li.menu-focus'));
            // Move to the first item in the child menu
            $newItemUL = $item.children('ul').first();
            $newItem = $newItemUL.children('li').first();


            // make sure the child menu is visible
            $newItemUL.attr('aria-hidden', 'false');
            this.bChildOpen = true;

            return $newItem;
        }

        // if $item is not the last item in its menu, move to the next item. If startChr is specified, move
        // to the next item with a title that begins with that character.
        //
        if (startChr) {

            var bMatch = false;
            var curNdx = menuIndex+1;

            // check if the active item was the last one on the list
            if (curNdx == menuNum) {
                curNdx = 0;
            }

            // Iterate through the menu items (starting from the current item and wrapping) until a match is found
            // or the loop returns to the current menu item
            while (curNdx != menuIndex)  {

                // Use the first of the two following lines if menu does not contain anchor tags.
                // Otherwise use the second
                // var titleChr = $menuItems.eq(curNdx).html().charAt(0);
                var titleChr = $menuItems.eq(curNdx).find('a').html().charAt(0);

                if (titleChr.toLowerCase() == startChr) {
                    bMatch = true;
                    break;
                }

                curNdx = curNdx+1;

                if (curNdx == menuNum) {
                    // reached the end of the list, start again at the beginning
                    curNdx = 0;
                }
            }

            if (bMatch == true) {
                $newItem = $menuItems.eq(curNdx);

                // remove the focus styling from the current item
                $item.removeClass('menu-focus');

                return $newItem
            }
            else {
                return $item;
            }
        }
        else {
            if (menuIndex < menuNum-1) {
                $newItem = $menuItems.eq(menuIndex+1);
            }
            else {
                $newItem = $menuItems.first();
            }
        }

        // remove the focus styling from the current item
        $item.removeClass('menu-focus');

        return $newItem;
    }

//
// Function moveUp() is a member function to select the previous item in a menu.
// If the active item is the first in the menu, this function will loop to the
// last menu item.
//
// @param($item object) $item is the active menu item
//
// @return (object) Returns the item to move to. Returns $item is no move is possible
//
    Menubar.prototype.moveUp = function($item) {

        var $itemUL = $item.parent(); // $item's containing menu
        var $menuItems = $itemUL.children('li').not('.separator'); // the items in the currently active menu
        var menuNum = $menuItems.length; // the number of items in the active menu
        var menuIndex = $menuItems.index($item); // the items index in its menu
        var $newItem = null;
        var $newItemUL = null;

        if ($itemUL.is('.root-level')) { // this is the root level menu

            // nothing to do
            return $item;
        }

        // if $item is not the first item in its menu, move to the previous item
        if (menuIndex > 0) {

            $newItem = $menuItems.eq(menuIndex-1);
        }
        else {
            // loop to top of menu
            $newItem = $itemUL.parent();
        }

        // remove the focus styling from the current item
        $item.removeClass('menu-focus');

        return $newItem;
    }

//
// Function handleKeyPress() is a member function to process keydown events
// for the menus.
//
// The Opera browser performs some window commands from the keypress event,
// not keydown like Firefox, Safari, and IE. This event handler consumes
// keypresses for relevant keys so that Opera behaves when the user is
// manipulating the menu with the keyboard.
//
// @param($item object) $menu is the jquery object of the item firing the event
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns false if consuming; true if propagating
//
    Menubar.prototype.handleKeyPress = function($item, e) {

        if (e.altKey || e.ctrlKey || e.shiftKey) {
            // Modifier key pressed: Do not process
            return true;
        }

        switch(e.keyCode) {
            case this.keys.tab: {
                return true;
            }
            case this.keys.esc:
            case this.keys.enter:
            case this.keys.space:
            case this.keys.up:
            case this.keys.down:
            case this.keys.left:
            case this.keys.right: {

                e.stopPropagation();
                return false;
            }
            default : {
                var chr = String.fromCharCode(e.which);

                this.$activeItem = this.moveDown($item, chr);
                this.$activeItem.focus();

                e.stopPropagation();
                return false;
            }
        } // end switch
        return true;

    } // end handleKeyPress()

//
// Function handleDocumentClick() is a member function to process click events on the document. Needed
// to close an open menu if a user clicks outside the menu
//
// @param(e object) e is the associated event object
//
// @return(boolean) Returns true;
//
    Menubar.prototype.handleDocumentClick = function(e) {

        // get a list of all child menus
        var $childMenus = this.$id.find('ul').not('.root-level');

        // hide the child menus
        $childMenus.attr('aria-hidden', 'true');

        //var $secondLevel = this.$id.find('li').attr('tabindex', '-1');
        this.$id.find('ul li > ul li > ul').remove();

        this.$allItems.removeClass('menu-focus');

        this.$activeItem = null;

        // allow the event to propagate
        return true;

    } // end handleDocumentClick()


/////////////// end menu widget definition /////////////////////


        $(window).bind("load resize", function() {
            var ww = window.innerWidth;
            if (ww >= 540) {
                var navgateway = new Menubar('block-lime-zurb-mainnavigation', false);
                // var navgateway = new Menubar('block-department-subpage-menu', false);
            }
            if (ww < 540) {
                $('.expander-content ul.menubar li.menu-item.menu-item--expanded > a').bind('touchstart click', function(event) {
                    // If submenu is not hidden
                    if ($(this).siblings('ul.menu.dropdown').attr('aria-hidden') == 'false') {
                        // HIDE it for screenreaders
                        $(this).siblings('ul.menu.dropdown').attr('aria-hidden', 'true');
                        // Toggle class for icon
                        $(this).parent('li.menu-item.menu-item--expanded').toggleClass('expanded');
                        // remove any other added links from the DOM
                        $('li.added-toplevel-link').remove();
                    // Otherwise Show it
                    } else {
                        $(this).siblings('ul.menu.dropdown').attr('aria-hidden', 'false');
                        $(this).parent('li.menu-item.menu-item--expanded').toggleClass('expanded');
                        $(this).siblings('ul.menu.dropdown').prepend('<li tabindex="-1" class="menu-item added-toplevel-link" role="menuitem"><a href=' + this + '>' + $(this).text() + '</a></li>');
                        var txt = $(this).text();
                        console.log('reading true ');
                        console.log(txt);
                    }
                    event.stopPropagation();
                    event.preventDefault();
                });
            }
        });

} (Drupal, jQuery, this));
