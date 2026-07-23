<style>
    .share-buttons {
        font-size: 0.7rem;
        line-height: 0.7rem;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0 0 0px;
        z-index: 2;
        position: relative;
        text-align: center;
        list-style-type: none;
        padding: 0;
        display: flex;
        flex-flow: row wrap;
        justify-content: space-between;
        align-content: flex-start;
    }

    .share-buttons li {
        height: auto;
        flex: 0 1 auto;
        width: calc(25% - 1px);
        margin-right: 1px;
        margin-bottom: 1px;
    }

    .share-buttons li:last-child {
        width: 25%;
        margin-right: 0;
    }

    .share-buttons svg {
        fill: #fff;
        margin-right: 5px;
        width: 16px;
        height: 16px;
    }

    .share-googleplus svg {
        width: 20px;
        height: 16px;
    }

    .share-buttons a {
        display: block;
        padding: 12px 12px 9px;
        text-align: center;
        color: #fff;
        font-size: 14px;
        border-radius: 3px;
        cursor: pointer;
    }

    .share-buttons i {
        color: #fff;
    }

    .share-twitter {
        background: #1da1f2;
    }

    .share-facebook {
        background: #3b5998;
    }

    .share-googleplus {
        background: #db4437;
    }

    .share-pinterest {
        background: #b5071a;
    }

    .share-tumblr {
        background: #34465d;
    }

    .share-email {
        background: #000;
    }

    .share-linkedin {
        background: #0077B5;
    }

    .share-linkedin {
        background: #0077B5;
    }

    .share-reddit {
        background: #ff5700;
    }
</style>
<script>
    $(document).ready(function() {
        var w = 600;
        var h = 400;
        var left = (screen.width / 2) - (w / 2);
        var top = (screen.height / 2) - (h / 2);

        $('.social-share-btn').each(function() {
            if ($(this).attr('onclick')) {
                $(this).attr('onclick', $(this).attr('onclick').replace('LEFT_POS', left));
                $(this).attr('onclick', $(this).attr('onclick').replace('TOP_POST', top));
            }
        });
    });
</script>
<div class="bg-white widget border rounded  text-left" id="social_share">

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <h3 class="h4 text-black widget-title mb-3"><?php echo mlx_get_lang('Social Media Share'); ?></h3>
        </div>
        <div class="form-group">


            <ul class="share-buttons">
                <li>
                    <a class="w-inline-block social-share-btn share-facebook" title="Share on Facebook" target="_blank" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-facebook"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-twitter" target="_blank" title="Tweet" onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ' :%20 ' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-twitter"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-googleplus" target="_blank" title="Share on Google+" onclick="window.open('https://plus.google.com/share?url=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-google-plus"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-pinterest" target="_blank" title="Pin it" onclick="window.open('http://pinterest.com/pin/create/button/?url=' + encodeURIComponent(document.URL) + '&description=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-pinterest"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-tumblr" target="_blank" title="Post to Tumblr" onclick="window.open('http://www.tumblr.com/share?v=3&u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-tumblr"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-email" target="_blank" title="Email" onclick="window.open('mailto:?subject=' + encodeURIComponent(document.title) + '&body=' + encodeURIComponent(document.URL), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-envelope"></i>
                    </a>
                </li>

                <li>
                    <a class="w-inline-block social-share-btn share-linkedin" target="_blank" title="Share on LinkedIn" onclick="window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(document.URL) + '&title=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-linkedin"></i>
                    </a>
                </li>
                <li>
                    <a class="w-inline-block social-share-btn share-reddit" target="_blank" title="Submit to Reddit" onclick="window.open('http://www.reddit.com/submit?url=' + encodeURIComponent(document.URL) + '&title=' + encodeURIComponent(document.title), 'targetWindow', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=600,height=400,left=LEFT_POS,top=TOP_POST'); return false;">
                        <i class="fa fa-reddit"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>