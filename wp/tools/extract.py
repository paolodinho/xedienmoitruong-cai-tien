"""Tach du lieu tu HTML da crawl (xedienmoitruong.vn, CMS PHP cu ban bootstrap/uikit) -> tham-khao/data/site.json + import.json
Cung schema voi ban cabinbaove de dung chung import.php. Giu nguyen slug (ke ca chu hoa)."""
import json, os, re
from collections import Counter
from bs4 import BeautifulSoup

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
DATA = os.path.join(ROOT, 'tham-khao', 'data')
HTML = os.path.join(ROOT, 'tham-khao', 'html')
BASE = 'https://xedienmoitruong.vn'
crawl = json.load(open(os.path.join(DATA, 'crawl.json')))


def slug_of(href):
    p = re.sub(r'^https?://(www\.)?xedienmoitruong\.vn', '', (href or '').strip()).split('?')[0].split('#')[0].strip('/')
    return re.sub(r'\.html$', '', p)


def clean_html(node):
    if node is None:
        return ''
    for t in node.select('script, style, .meta, #tocDiv, .share-box, .viewed'):
        t.decompose()
    for t in node.find_all('iframe'):
        if 'youtube' not in (t.get('src') or ''):
            t.decompose()
    for t in node.find_all(True):
        st = t.get('style')
        if st:
            keep = [d for d in st.split(';') if d.strip().split(':')[0].strip().lower() in ('text-align',) and t.name in ('p', 'td', 'th', 'div', 'h2', 'h3')]
            if keep:
                t['style'] = ';'.join(x.strip() for x in keep)
            else:
                del t['style']
        for a in ('class', 'id', 'face', 'size', 'color'):
            if a in t.attrs and t.name != 'iframe':
                del t[a]
    for t in node.find_all(['span', 'font']):
        if not t.attrs:
            t.unwrap()
    out = node.decode_contents().strip()
    out = re.sub(r'(&nbsp;|\xa0)+', ' ', out)
    out = re.sub(r'<p>\s*</p>', '', out)
    out = out.replace('—', '-').replace('–', '-')
    out = re.sub(r'href="https?://(www\.)?xedienmoitruong\.vn/', 'href="/', out)
    return out


def imgs(node):
    return [i.get('data-src') or i.get('src') for i in (node.find_all('img') if node else []) if (i.get('data-src') or i.get('src'))]


def meta(soup, name, attr='name'):
    m = soup.find('meta', attrs={attr: name})
    return (m.get('content') or '').strip() if m else ''


def crumbs(soup):
    b = soup.select_one('.uk-breadcrumb')
    return [(a.get_text(strip=True), slug_of(a.get('href'))) for a in b.select('li a')][1:] if b else []


def rel(u):
    return re.sub(r'^https?://(www\.)?xedienmoitruong\.vn', '', u or '')


def load(u):
    return BeautifulSoup(open(os.path.join(HTML, crawl[u]['file']), encoding='utf8', errors='ignore').read(), 'lxml')


items, thumbs, dates, exc = [], {}, {}, {}
pages = {}
for url, v in crawl.items():
    if v['code'] != 200 or not v['file'] or v['final'] != url:
        continue
    if not (url.endswith('.html') or url == BASE + '/'):
        continue
    m = re.search(r'^(.*)/trang-(\d+)\.html$', url)
    if m:
        pages.setdefault(slug_of(m.group(1)), []).append(url)
        continue
    soup = load(url)
    canon = soup.find('link', rel='canonical')
    it = {'url': url, 'slug': slug_of(url), 'title_tag': v['title'], 'meta_desc': meta(soup, 'description'),
          'og_image': rel(meta(soup, 'og:image', 'property')), 'canonical': canon.get('href') if canon else '', 'crumbs': crumbs(soup)}
    # Thu anh + ngay tu cac the danh sach (dung cho bai/SP)
    for a in soup.select('.article-item, .product-item, .product-1'):
        t = a.select_one('.article-title a, .product-title a, a')
        if not t:
            continue
        s = slug_of(t.get('href'))
        im = a.select_one('img')
        if im:
            thumbs.setdefault(s, rel(im.get('data-src') or im.get('src') or ''))
        tm = a.select_one('.time')
        if tm:
            mm = re.search(r'(\d{2})/(\d{2})/(\d{4})', tm.get_text())
            if mm:
                dates.setdefault(s, f'{mm.group(3)}-{mm.group(2)}-{mm.group(1)} 09:00:00')
        de = a.select_one('.article-description')
        if de:
            exc.setdefault(s, de.get_text(' ', strip=True))
    if url == BASE + '/':
        it.update(type='home')
    elif soup.select_one('.productDetailContent'):
        buy = soup.select_one('.productDetail-buy')
        gal = [rel(li.get('data-thumb')) for li in soup.select('#image-gallery li[data-thumb]')]
        art = soup.select_one('.productDetailContent')
        ex = buy.select_one('.description') if buy else None
        price = buy.select_one('.price .new') if buy else None
        h1 = soup.select_one('.panel-head h1.title, h1.title, h1')
        it.update(type='product', h1=h1.get_text(strip=True), attrs={}, gallery=gal, excerpt=clean_html(ex),
                  content=clean_html(art), content_imgs=[rel(x) for x in imgs(art)], price=price.get_text(strip=True) if price else '')
        mo = re.search(r'Model\s*:\s*([A-Za-z0-9][A-Za-z0-9 .\-/]{1,30})', BeautifulSoup(it['excerpt'] + it['content'], 'lxml').get_text(' '))
        if mo:
            it['attrs']['Model'] = re.sub(r'\s+', '', mo.group(1)).strip('.-')
    elif soup.select_one('.content-detail-new'):
        art = soup.select_one('.content-detail-new')
        h1 = soup.select_one('article h1.title, h1')
        tm = soup.select_one('.article .time, .time')
        mm = re.search(r'(\d{2})/(\d{2})/(\d{4})', tm.get_text()) if tm else None
        it.update(type='post', h1=h1.get_text(strip=True) if h1 else v['title'], excerpt='', content=clean_html(art),
                  content_imgs=[rel(x) for x in imgs(art)], date=f'{mm.group(3)}-{mm.group(2)}-{mm.group(1)} 09:00:00' if mm else '')
    elif soup.select_one('section.home-product') and soup.select_one('h1.heading-cat'):
        sec = soup.select_one('section.home-product')
        it.update(type='product_cat', name=soup.select_one('h1.heading-cat').get_text(strip=True),
                  description=clean_html(sec.select_one('.intro')), products=[slug_of(a.get('href')) for a in sec.select('.product-title a')])
    elif soup.select_one('.article-item'):
        h1 = soup.select_one('h1')
        it.update(type='category', name=h1.get_text(strip=True) if h1 else v['title'],
                  posts=[slug_of(a.get('href')) for a in soup.select('.article-item .article-title a')])
    elif it['slug'] == 'lien-he':
        it.update(type='contact', content='')
    else:
        it.update(type='other')
    items.append(it)

# Gop phan trang /slug/trang-N.html
for base, urls in pages.items():
    parent = next((x for x in items if x['slug'] == base), None)
    if not parent:
        continue
    for u in urls:
        s = load(u)
        if parent['type'] == 'product_cat':
            parent['products'] += [slug_of(a.get('href')) for a in s.select('section.home-product .product-title a')]
        elif parent['type'] == 'category':
            parent['posts'] += [slug_of(a.get('href')) for a in s.select('.article-item .article-title a')]
        for a in s.select('.article-item, .product-item'):
            t = a.select_one('.article-title a, .product-title a')
            im = a.select_one('img')
            if t and im:
                thumbs.setdefault(slug_of(t.get('href')), rel(im.get('src') or ''))
            tm = a.select_one('.time')
            if t and tm:
                mm = re.search(r'(\d{2})/(\d{2})/(\d{4})', tm.get_text())
                if mm:
                    dates.setdefault(slug_of(t.get('href')), f'{mm.group(3)}-{mm.group(2)}-{mm.group(1)} 09:00:00')
for x in items:
    for k in ('products', 'posts'):
        if k in x:
            x[k] = list(dict.fromkeys(x[k]))
json.dump(items, open(os.path.join(DATA, 'site.json'), 'w'), ensure_ascii=False, indent=1)

# ---- import.json (tuong duong prep.py ban cabin) ----
S = {x['slug']: x for x in items}
pcat, acat = {}, {}
for x in items:
    if x['type'] == 'product_cat' and x['slug'] != 'san-pham':
        for s in x['products']:
            pcat.setdefault(s, []).append(x['slug'])
    if x['type'] == 'category':
        for s in x['posts']:
            acat.setdefault(s, []).append(x['slug'])
cat_slugs = {x['slug'] for x in items if x['type'] == 'category'}
pc_slugs = {x['slug'] for x in items if x['type'] == 'product_cat'}
for x in items:
    if x['type'] == 'product':
        cs = pcat.get(x['slug'], []) or [c[1] for c in x['crumbs'][-1:] if c[1] in pc_slugs and c[1] != 'san-pham']
        x['cats'] = cs
        x['featured'] = (x['gallery'] or x['content_imgs'] or [thumbs.get(x['slug'], '')])[0]
    if x['type'] == 'post':
        x['cats'] = acat.get(x['slug'], []) or [c[1] for c in x['crumbs'][-1:] if c[1] in cat_slugs]
        x['date'] = x.get('date') or dates.get(x['slug'], '')
        x['excerpt'] = exc.get(x['slug'], '') or x['meta_desc']
        og = x['og_image']
        x['featured'] = thumbs.get(x['slug']) or (og if og and 'logo' not in og else '') or (x['content_imgs'] or [''])[0]
    if x['type'] == 'product_cat':
        cr = [c for c in x['crumbs'] if c[1] != 'san-pham']
        x['parent'] = '' if x['slug'] == 'san-pham' or len(cr) < 2 else cr[-2][1]
json.dump(items, open(os.path.join(DATA, 'import.json'), 'w'), ensure_ascii=False, indent=1)

print(Counter(x['type'] for x in items))
for x in items:
    if x['type'] in ('product_cat', 'category'):
        print(x['type'], x['slug'], '|', x['name'], '|', len(x.get('products', x.get('posts', []))), 'parent=' + x.get('parent', ''), [c[0] for c in x['crumbs']])
posts = [x for x in items if x['type'] == 'post']
prods = [x for x in items if x['type'] == 'product']
print('posts', len(posts), 'co ngay', sum(1 for p in posts if p['date']), 'co anh', sum(1 for p in posts if p['featured']), 'khong cat', [p['slug'] for p in posts if not p['cats']][:20])
print('SP', len(prods), 'co anh', sum(1 for p in prods if p['featured']), 'co model', sum(1 for p in prods if p['attrs']), 'khong cat', [p['slug'] for p in prods if not p['cats']][:20])
print('other', [x['slug'] for x in items if x['type'] == 'other'])
