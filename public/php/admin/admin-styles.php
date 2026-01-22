<style>

/* ============================
   VARIABLES GLOBALES DE LA WEB
============================ */
:root {
    --font-sans: -apple-system, blinkmacsystemfont, "Segoe UI", roboto,
        "Helvetica Neue", arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji",
        "Segoe UI Symbol";
    --font-serif: "Georgia", "Times New Roman", serif;

    --color-background: #fff;
    --color-text: #333;
    --color-component: #2E7D32;
    --color-component-hover: #1B5E20;
    --color-component-border: #000;
    --color-footer: #000;
    --color-footer-text: #CCC;
    --color-form: #f6f2f2;
    --color-star-yellow: #f1d628;
    --color-star-grey: #CCC;
    --color-shadow: rgb(0 0 0 / 20%);
    --color-overlay: rgb(0 0 0 / 50%);
    --color-comment: #444;
    --color-comment-date: #666;
    --color-comment-empty: #999;
    --color-visor-overlay: rgb(0 0 0 / 80%);
    --color-arrow-background: rgb(255 255 255 / 20%);
    --color-arrow-background-hover: rgb(255 255 255 / 40%);
    --color-danger: #C62828;
    --color-danger-hover: #8E0000;
    --color-th: #f4f4f4;
    --color-th-even: #fafafa;

    --max-width: 768px;
}

/* ============================
   ESTILO GENERAL
============================ */
body {
    
    font-family: var(--font-sans);
    background: var(----color-background);
    margin: 0;
    padding: 0;
    color: var(--color-text);
    line-height: 1.5;
}

.admin-panel {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 1.5rem;
}

/* ============================
   LOGIN
============================ */

.login-box {
    width: 100%;
    max-width: 380px;
    background: var(--color-form);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 6px var(--color-shadow);
    position: absolute; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%);
}

.login-title {
    font-size: 1.6rem;
    font-weight: 600;
    color: var(--color-text);
    margin-bottom: 1.5rem;
    text-align: center;
}

.error-box {
    background: #f8d7da;
    color: var(--color-danger);
    padding: 0.9rem 1.2rem;
    border-radius: 8px;
    border: 1px solid #f5c6cb;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    text-align: center;
}

/* ============================
   TARJETAS / SECCIONES
============================ */
.admin-section {
    background: var(--color-form);
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2.5rem;
    box-shadow: 0 2px 6px var(--color-shadow);

}

.admin-section__title {
    margin: 0 0 1.2rem;
    font-size: 1.6rem;
    font-weight: 600;
    color: var(--color-text);
}

/* ============================
   FORMULARIO
============================ */

.form__label {
    font-size: 0.95rem;
    font-weight: bold;
    margin-top: 1rem;
    display: block;
    color: var(--color-text);
}

.form__input {
    width: 100%;
    padding: 0.5rem;
    margin: 0.4rem 0;
    border: 1px solid var(--color-footer-text);
    border-radius: 4px;
    font-size: 1rem;
    background: var(--color-background);
    box-sizing: border-box;
    transition: border-color 0.2s, background 0.2s;
}

.form__input:focus {
    border-color: var(--color-component);
    background: var(--color-background);
    outline: none;
}

.form__note {
    display: block;
    color: var(--color-comment);
}

/* ============================
   BOTONES
============================ */
.button {
    background: var(--color-component);
    color: var(--color-background); 
    padding: 0.65rem 1.2rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.95rem;
    margin-top: 1rem;
    transition: background 0.2s, transform 0.1s;
    text-decoration: none;
}

.button:hover {
    background: var(--color-component-hover);
    transform: translateY(-1px);
}

.button--danger {
    background: var(--color-danger);
}

.button--danger:hover {
    background: var(--color-danger-hover);
}

/* ============================
   LISTAS DEL EDITOR VISUAL
============================ */
.editor-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
    margin-top: 0.8rem;
}

.editor-item {
    display: flex;
    gap: 0.8rem;
    align-items: center;
}

.editor-item input {
    flex: 1;
}

/* ============================
   CHECKBOXES
============================ */
.admin-section label {
    display: block;
    margin-top: 0.7rem;
    font-size: 0.95rem;
    color: var(--color-text);
}

.admin-section input[type="checkbox"] {
    margin-right: 0.45rem;
    transform: scale(1.15);
    accent-color: var(--color-component);
}

/* ============================
   MENSAJE OK
============================ */
.ok {
    background: #d4edda;
    color: var(--color-component);
    padding: 0.9rem 1.2rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    border: 1px solid #c3e6cb;
    font-size: 0.95rem;
}

/* ============================
   MENÚ DE NAVEGACIÓN
============================ */
.admin-nav {
    background: var(--color-background);
    border-bottom: 1px solid #e6e9ef;
    padding: 0.8rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}

.admin-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 1.5rem;
}

.admin-nav a {
    text-decoration: none;
    color: var(--color-component);
    font-weight: 600;
    font-size: 0.95rem;
    padding: 0.4rem 0.2rem;
    transition: color 0.2s;
}

.admin-nav a:hover {
    color: var(--color-component-hover);
}

@media (max-width: 768px) { 
    .admin-nav ul { 
        flex-direction: column; 
        gap: 0.5rem; 
    } 
    .admin-nav a { 
        display: block; 
        padding: 10px; 
        border-radius: 6px; 
    } 
}
/* ============================
   TABLAS
============================ */

.admin-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
    min-width: 700px; 
}

.admin-table th,
.admin-table td {
    padding: 12px 20px;
    text-align: left;
    white-space: nowrap; 
}

.admin-table th {
    background: var(--color-th);
    font-weight: 600;
}

.admin-table tr:nth-child(even) {
    background: var(--color-th-even);
}

.icon__item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    color: #333;
    margin: 1rem 0;
}

.icon__item--success i {
    color: var(--color-component);
}

.icon__item--error i {
    color: var(--color-danger); 
}

.icon__item span {
    flex: 1;
    font-size: 1rem;
}

</style>



