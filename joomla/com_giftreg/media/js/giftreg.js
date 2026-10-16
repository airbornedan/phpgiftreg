// The "..." comment buttons: click to show the comment beside it, click again (or elsewhere) to hide.
document.addEventListener('click', function (e) {
	var btn = e.target.closest('.giftreg [data-gr-comment]');
	document.querySelectorAll('.giftreg .gr-popover').forEach(function (p) {
		if (!btn || p.parentNode !== btn) p.remove();
	});
	if (!btn) return;
	e.preventDefault();
	if (btn.querySelector('.gr-popover')) { btn.querySelector('.gr-popover').remove(); return; }
	var pop = document.createElement('div'), t = document.createElement('h3'), c = document.createElement('div');
	pop.className = 'gr-popover'; t.className = 'gr-popover-title'; c.className = 'gr-popover-content';
	t.textContent = 'Comment'; c.textContent = btn.getAttribute('data-gr-comment');
	pop.append(t, c); btn.append(pop);
});
