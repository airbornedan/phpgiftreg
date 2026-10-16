"""Pull the old giftreg theme rules we use out of Bootstrap 2 and scope them: classes get a gr- prefix,
everything sits under .giftreg, so Joomla's Bootstrap 5 can't collide."""
import re, sys
src = open(sys.argv[1]).read()
src = re.sub(r'/\*.*?\*/', '', src, flags=re.S)
CLASSES = set('''well page-header table table-bordered table-striped table-hover btn btn-small btn-primary btn-link
alert alert-info alert-block alert-success alert-error navbar navbar-inner brand nav pull-right pull-left
form-horizontal control-group control-label controls form-actions help-block help-inline checkbox input-xlarge
muted text-right warning active caret'''.split())
ELEMENTS = set('body a h1 h2 h3 h4 h5 h6 p ul ol li table th td tr thead tbody caption img legend fieldset label input select textarea button strong b i em small'.split())
# split top-level blocks
rules, i, depth, start = [], 0, 0, 0
while i < len(src):
    c = src[i]
    if c == '{':
        depth += 1
    elif c == '}':
        depth -= 1
        if depth == 0:
            rules.append(src[start:i + 1]); start = i + 1
    elif c == ';' and depth == 0:
        start = i + 1                      # @import / @charset
    i += 1
out = []
def keep(sel):
    sel = sel.strip()
    if not sel or '::-' in sel or ':-' in sel or '*' in sel:
        return None
    classes = re.findall(r'\.([A-Za-z][\w-]*)', sel)
    if any(c not in CLASSES for c in classes):
        return None
    elems = re.findall(r'(?:^|[\s>+~])([a-z][a-z0-9]*)', sel)
    if any(e not in ELEMENTS for e in elems):
        return None
    if not classes and not elems:
        return None
    if re.search(r'\[', sel) and not re.search(r'\[type=', sel):
        return None
    sel = re.sub(r'\.([A-Za-z][\w-]*)', r'.gr-\1', sel)
    if sel == 'body':
        return '.giftreg'
    if re.match(r'(html|body)\b', sel):
        return None
    return '.giftreg ' + sel
for r in rules:
    if r.lstrip().startswith('@'):
        continue
    sel, body = r.split('{', 1)
    sels = [s for s in (keep(x) for x in sel.split(',')) if s]
    if not sels:
        continue
    decls = [d for d in body.rstrip('}').split(';') if d.strip() and not d.strip().startswith(('*', '_'))
             and 'progid' not in d and 'expression(' not in d]
    if decls:
        out.append(','.join(sels) + '{' + ';'.join(decls) + '}')
print('/* Old phpgiftreg theme (Bootstrap 2.3.2 + Bootswatch, Apache 2.0), scoped to .giftreg with gr- classes. */')
print('\n'.join(out))
