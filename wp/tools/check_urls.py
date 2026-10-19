"""Kiem tra moi URL cu cua xedienmoitruong.vn tren ban moi (mac dinh Local): URL 200 cu phai 200 (hoac 301 -> 200), URL cu da chuyen huong phai 301 -> 200. 0 loi 404."""
import json, os, sys, http.client, urllib.parse
ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
BASE = sys.argv[1] if len(sys.argv) > 1 else 'http://xedienmoitruong.local'
host = urllib.parse.urlparse(BASE).netloc
def req(path):
    c = http.client.HTTPConnection(host, timeout=60)
    c.request('GET', urllib.parse.quote(path, safe="/?=&%.-_~()"), headers={'Host': host, 'User-Agent': 'check'})
    r = c.getresponse(); r.read(); return r.status, r.getheader('Location') or ''
crawl = json.load(open(f'{ROOT}/tham-khao/data/crawl.json'))
bad, n = [], 0
for u, v in crawl.items():
    if v['code'] != 200: continue
    path = urllib.parse.unquote(u.replace('https://xedienmoitruong.vn', '') or '/')
    st, loc = req(path); n += 1
    if st in (301, 302):
        st2, _ = req(urllib.parse.unquote(urllib.parse.urlparse(loc).path) or '/')
        if st2 != 200: bad.append((path, st, loc, st2))
    elif st != 200: bad.append((path, st, loc, 'cu: ' + v['final']))
print('xedienmoitruong.vn:', n, 'URL, loi:', len(bad)); [print(' ', b) for b in bad[:60]]
