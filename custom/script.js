const toolbar = document.querySelector('.toolbar-vertical');

if (toolbar) {
    // Fissa solo la toolbar
    toolbar.style.position = 'fixed';
    toolbar.style.top = '0';
    toolbar.style.left = '0';
    toolbar.style.height = '100vh';
    toolbar.style.width = '80px';
    toolbar.style.overflow = 'hidden';
    toolbar.style.zIndex = '1000';
    
    // Aggiungi margine al body per far spazio alla toolbar
    document.body.style.marginLeft = '80px';
    
    // Rimuovi qualsiasi overflow dal body (lascia lo scroll naturale)
    document.body.style.overflow = 'auto';
}