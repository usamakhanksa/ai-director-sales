'use strict';

class PropertyManager {
    constructor() {
        this.cookieName = window.currentLanguage + '_recently_viewed_properties';
        this.propertyId = $('div[data-property-id]').data('property-id');
        this.recentPropertyCookies = decodeURIComponent(this.getCookie(this.cookieName));
        this.arrList = [];
    }

    handleRecentlyViewedProperties() {
        if (this.recentPropertyCookies != null && this.recentPropertyCookies != undefined && this.recentPropertyCookies.length > 0) {
            this.arrList = JSON.parse(this.getCookie(this.cookieName));
        }

        if (this.propertyId != null && this.propertyId != 0 && this.propertyId != undefined) {
            let item = { id: this.propertyId };
            if (this.recentPropertyCookies == undefined || this.recentPropertyCookies == null || this.recentPropertyCookies == '') {
                this.arrList.push(item);
                this.setCookie(this.cookieName, JSON.stringify(this.arrList), 60);
            } else {
                this.arrList = JSON.parse(this.recentPropertyCookies);
                var index = this.arrList.map(function (e) {
                    return e.id;
                }).indexOf(item.id);

                if (index === -1) {
                    if (this.arrList.length >= 20) {
                        this.arrList.shift();
                    }

                    this.arrList.push(item);
                    this.clearCookies(this.cookieName);
                    this.setCookie(this.cookieName, JSON.stringify(this.arrList), 60);
                } else {
                    this.arrList.splice(index, 1);
                    this.arrList.push(item);

                    this.clearCookies(this.cookieName);
                    this.setCookie(this.cookieName, JSON.stringify(this.arrList), 60);
                }
            }
        }
    }

    setCookie(cname, cvalue, exdays) {
        var d = new Date();
        var url = new URL(window.siteUrl);
        d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
        var expires = 'expires=' + d.toUTCString();
        document.cookie = cname + '=' + cvalue + '; ' + expires + '; path=/' + '; domain=' + url.hostname;
    }

    getCookie(cname) {
        var name = cname + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    }

    clearCookies(name) {
        var url = new URL(window.siteUrl);
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/' + '; domain=' + url.hostname;
    }

    parseCookie() {
        var cookieName = window.currentLanguage + '_wishlist';
        var wishListCookies = decodeURIComponent(this.getCookie(cookieName));

        if (wishListCookies != null && wishListCookies != undefined && !!wishListCookies) {
            wishListCookies = JSON.parse(wishListCookies);
        }
    }

    getProperties() {
        var $container = $('#property-component')
        var url = $container.data('url')
        var type = $container.data('type')
        var isLoading = true
        var data = []
        var property_id = null
        var project_id = null
        var show_empty_string = false
        var urlWithParams = url + '?type=' + type

        if (property_id) {
            urlWithParams += '&property_id=' + property_id
        }

        if (project_id) {
            urlWithParams += '&project_id=' + project_id
        }

        $.ajax({
            url: urlWithParams,
            method: 'GET',
        }).done(function(res) {
            data = res.data ? res.data : [];
            isLoading = false;

            // Update the DOM
            $container.empty();

            if (isLoading) {
                $container.append(
                    '<div class="half-circle-spinner"><div class="circle circle-1"></div><div class="circle circle-2"></div></div>'
                );
            } else if (show_empty_string && !data.length) {
                $container.append('<div class="col-12 text-center"><span>No property found!</span></div>');
            } else {
                data.forEach(function(item) {
                    var $item = $('<div class="col-lg-4 col-md-6 col-sm-12 item-recent"></div>');
                    $item.html(item.HTML);
                    $container.append($item);
                });
            }
        });
    }
}

$(document).ready(function () {
    const propertyManager = new PropertyManager();
    propertyManager.handleRecentlyViewedProperties();
    if ($('#property-component').length) {
        propertyManager.parseCookie();
        propertyManager.getProperties();
    }
})
