---
layout: 
title: Gift
h1-title: Gifts
meta-title: "Gifts for Family, girls, boys, lovers"
permalink: "/gifts/"
published: true
allow_search_engine: false
sitemap: false
---
<div class="row blog">
    {% if page.description %} {{ page.description }} {% endif %}
      {% for item in site.giftcollections  %}
            <article class="card border-0 col-lg-6 mb-7">
                <div class="card-body p-0">
                  <div class="content-image mb-lg-5 rounded">
                    <a href="{{ site.url }}{{ item.url }}">
                        <img data-src="{{ item.image }}" src="data:image/gif;base64,R0lGODdhAQABAPAAAMPDwwAAACwAAAAAAQABAAACAkQBADs=" class="img-fluid w-100 rounded lazyload" alt="{{ item.title }}">
                    </a>
                  </div>
                  <small class="d-block text-secondary mb-1">{{ item.date | date: '%B %d, %Y' }}</small>
                  <h3 class="h5">
                    <a href="{{ site.url }}{{ item.url }}">{{ item.title }}</a>
                  </h3>
                  <p>{{ item.description }}</p>
                </div>
              </article>
      {% endfor %}
    <hr>
</div>