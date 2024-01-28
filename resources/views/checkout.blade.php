<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Xendivel Cards Payment Template</title>

        {{-- @vite('resources/css/checkout.css') --}}
        <style>
            /*! tailwindcss v3.3.5 | MIT License | https://tailwindcss.com*/*,:after,:before{box-sizing:border-box;border:0 solid #e5e7eb}:after,:before{--tw-content:""}html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:initial}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button;background-color:initial;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:initial}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0}fieldset,legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}[multiple],[type=date],[type=datetime-local],[type=email],[type=month],[type=number],[type=password],[type=search],[type=tel],[type=text],[type=time],[type=url],[type=week],input:where(:not([type])),select,textarea{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border-color:#6b7280;border-width:1px;border-radius:0;padding:.5rem .75rem;font-size:1rem;line-height:1.5rem;--tw-shadow:0 0 #0000}[multiple]:focus,[type=date]:focus,[type=datetime-local]:focus,[type=email]:focus,[type=month]:focus,[type=number]:focus,[type=password]:focus,[type=search]:focus,[type=tel]:focus,[type=text]:focus,[type=time]:focus,[type=url]:focus,[type=week]:focus,input:where(:not([type])):focus,select:focus,textarea:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow);border-color:#2563eb}input::-moz-placeholder,textarea::-moz-placeholder{color:#6b7280;opacity:1}input::placeholder,textarea::placeholder{color:#6b7280;opacity:1}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-date-and-time-value{min-height:1.5em;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit,::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-meridiem-field,::-webkit-datetime-edit-millisecond-field,::-webkit-datetime-edit-minute-field,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-second-field,::-webkit-datetime-edit-year-field{padding-top:0;padding-bottom:0}select{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.5em 1.5em;padding-right:2.5rem;-webkit-print-color-adjust:exact;print-color-adjust:exact}[multiple],[size]:where(select:not([size="1"])){background-image:none;background-position:0 0;background-repeat:unset;background-size:initial;padding-right:.75rem;-webkit-print-color-adjust:unset;print-color-adjust:unset}[type=checkbox],[type=radio]{-webkit-appearance:none;-moz-appearance:none;appearance:none;padding:0;-webkit-print-color-adjust:exact;print-color-adjust:exact;display:inline-block;vertical-align:middle;background-origin:border-box;-webkit-user-select:none;-moz-user-select:none;user-select:none;flex-shrink:0;height:1rem;width:1rem;color:#2563eb;background-color:#fff;border-color:#6b7280;border-width:1px;--tw-shadow:0 0 #0000}[type=checkbox]{border-radius:0}[type=radio]{border-radius:100%}[type=checkbox]:focus,[type=radio]:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:2px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}[type=checkbox]:checked,[type=radio]:checked{border-color:#0000;background-color:currentColor;background-size:100% 100%;background-position:50%;background-repeat:no-repeat}[type=checkbox]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3E%3Cpath d='M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=checkbox]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=radio]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3E%3Ccircle cx='8' cy='8' r='3'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=radio]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:checked:focus,[type=checkbox]:checked:hover,[type=checkbox]:indeterminate,[type=radio]:checked:focus,[type=radio]:checked:hover{border-color:#0000;background-color:currentColor}[type=checkbox]:indeterminate{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3E%3Cpath stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3E%3C/svg%3E");background-size:100% 100%;background-position:50%;background-repeat:no-repeat}@media (forced-colors:active) {[type=checkbox]:indeterminate{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:indeterminate:focus,[type=checkbox]:indeterminate:hover{border-color:#0000;background-color:currentColor}[type=file]{background:unset;border-color:inherit;border-width:0;border-radius:0;padding:0;font-size:unset;line-height:inherit}[type=file]:focus{outline:1px solid ButtonText;outline:1px auto -webkit-focus-ring-color}*,::backdrop,:after,:before{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#3b82f680;--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: }.container{width:100%}@media (min-width:640px){.container{max-width:640px}}@media (min-width:768px){.container{max-width:768px}}@media (min-width:1024px){.container{max-width:1024px}}@media (min-width:1280px){.container{max-width:1280px}}@media (min-width:1536px){.container{max-width:1536px}}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}.fixed{position:fixed}.absolute{position:absolute}.relative{position:relative}.inset-0{inset:0}.end-0{inset-inline-end:0}.left-0{left:0}.right-0{right:0}.start-0{inset-inline-start:0}.top-0{top:0}.top-1\/2{top:50%}.z-0{z-index:0}.z-10{z-index:10}.z-40{z-index:40}.z-50{z-index:50}.order-2{order:2}.col-span-1{grid-column:span 1/span 1}.col-span-2{grid-column:span 2/span 2}.col-span-3{grid-column:span 3/span 3}.col-span-6{grid-column:span 6/span 6}.mx-6{margin-left:1.5rem;margin-right:1.5rem}.mx-auto{margin-left:auto;margin-right:auto}.my-2{margin-top:.5rem;margin-bottom:.5rem}.-me-0{margin-inline-end:0}.-me-0\.5{margin-inline-end:-.125rem}.-me-2{margin-inline-end:-.5rem}.-ml-px{margin-left:-1px}.-mt-px{margin-top:-1px}.mb-2{margin-bottom:.5rem}.mb-4{margin-bottom:1rem}.mb-6{margin-bottom:1.5rem}.me-1{margin-inline-end:.25rem}.ml-1{margin-left:.25rem}.ml-12{margin-left:3rem}.ml-2{margin-left:.5rem}.ml-3{margin-left:.75rem}.ml-4{margin-left:1rem}.ml-auto{margin-left:auto}.mr-10{margin-right:2.5rem}.mr-2{margin-right:.5rem}.ms-2{margin-inline-start:.5rem}.ms-3{margin-inline-start:.75rem}.ms-4{margin-inline-start:1rem}.mt-1{margin-top:.25rem}.mt-16{margin-top:4rem}.mt-2{margin-top:.5rem}.mt-3{margin-top:.75rem}.mt-4{margin-top:1rem}.mt-6{margin-top:1.5rem}.mt-8{margin-top:2rem}.mt-auto{margin-top:auto}.block{display:block}.inline-block{display:inline-block}.flex{display:flex}.inline-flex{display:inline-flex}.table{display:table}.grid{display:grid}.hidden{display:none}.h-10{height:2.5rem}.h-16{height:4rem}.h-20{height:5rem}.h-3\/4{height:75%}.h-4{height:1rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-7{height:1.75rem}.h-8{height:2rem}.h-9{height:2.25rem}.h-full{height:100%}.h-screen{height:100vh}.min-h-screen{min-height:100vh}.w-1\/2{width:50%}.w-10{width:2.5rem}.w-14{width:3.5rem}.w-16{width:4rem}.w-20{width:5rem}.w-24{width:6rem}.w-3\/4{width:75%}.w-4{width:1rem}.w-4\/12{width:33.333333%}.w-48{width:12rem}.w-5{width:1.25rem}.w-6{width:1.5rem}.w-7{width:1.75rem}.w-8{width:2rem}.w-\[500px\]{width:500px}.w-\[600px\]{width:600px}.w-auto{width:auto}.w-full{width:100%}.max-w-2xl{max-width:42rem}.max-w-6xl{max-width:72rem}.max-w-7xl{max-width:80rem}.max-w-xl{max-width:36rem}.flex-1{flex:1 1 0%}.shrink-0{flex-shrink:0}.border-collapse{border-collapse:collapse}.origin-top{transform-origin:top}.-translate-x-1\/2{--tw-translate-x:-50%}.-translate-x-1\/2,.-translate-y-1\/2{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.-translate-y-1\/2{--tw-translate-y:-50%}.translate-y-0{--tw-translate-y:0px}.translate-y-0,.translate-y-4{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.translate-y-4{--tw-translate-y:1rem}.scale-100{--tw-scale-x:1;--tw-scale-y:1}.scale-100,.scale-95{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.scale-95{--tw-scale-x:.95;--tw-scale-y:.95}.transform{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.cursor-default{cursor:default}.cursor-pointer{cursor:pointer}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}.grid-cols-6{grid-template-columns:repeat(6,minmax(0,1fr))}.flex-col{flex-direction:column}.place-content-center{place-content:center}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-end{justify-content:flex-end}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.justify-items-center{justify-items:center}.gap-2{gap:.5rem}.gap-3{gap:.75rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}.gap-8{gap:2rem}.gap-x-4{-moz-column-gap:1rem;column-gap:1rem}.gap-y-2{row-gap:.5rem}.gap-y-4{row-gap:1rem}.space-x-8>:not([hidden])~:not([hidden]){--tw-space-x-reverse:0;margin-right:calc(2rem*var(--tw-space-x-reverse));margin-left:calc(2rem*(1 - var(--tw-space-x-reverse)))}.space-y-1>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(.25rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(.25rem*var(--tw-space-y-reverse))}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1.5rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem*var(--tw-space-y-reverse))}.divide-y>:not([hidden])~:not([hidden]){--tw-divide-y-reverse:0;border-top-width:calc(1px*(1 - var(--tw-divide-y-reverse)));border-bottom-width:calc(1px*var(--tw-divide-y-reverse))}.divide-gray-200>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(229 231 235/var(--tw-divide-opacity))}.self-center{align-self:center}.overflow-hidden{overflow:hidden}.overflow-y-auto{overflow-y:auto}.whitespace-nowrap{white-space:nowrap}.whitespace-pre-wrap{white-space:pre-wrap}.rounded{border-radius:.25rem}.rounded-full{border-radius:9999px}.rounded-lg{border-radius:.5rem}.rounded-md{border-radius:.375rem}.rounded-xl{border-radius:.75rem}.rounded-l-md{border-top-left-radius:.375rem;border-bottom-left-radius:.375rem}.rounded-r-md{border-top-right-radius:.375rem;border-bottom-right-radius:.375rem}.rounded-bl-md{border-bottom-left-radius:.375rem}.rounded-br-md{border-bottom-right-radius:.375rem}.rounded-tl-md{border-top-left-radius:.375rem}.rounded-tr-md{border-top-right-radius:.375rem}.border{border-width:1px}.border-b{border-bottom-width:1px}.border-b-2{border-bottom-width:2px}.border-l{border-left-width:1px}.border-l-4{border-left-width:4px}.border-r{border-right-width:1px}.border-t{border-top-width:1px}.border-none{border-style:none}.border-blue-500{--tw-border-opacity:1;border-color:rgb(59 130 246/var(--tw-border-opacity))}.border-blue-600{--tw-border-opacity:1;border-color:rgb(37 99 235/var(--tw-border-opacity))}.border-gray-100{--tw-border-opacity:1;border-color:rgb(243 244 246/var(--tw-border-opacity))}.border-gray-200{--tw-border-opacity:1;border-color:rgb(229 231 235/var(--tw-border-opacity))}.border-gray-300{--tw-border-opacity:1;border-color:rgb(209 213 219/var(--tw-border-opacity))}.border-gray-400{--tw-border-opacity:1;border-color:rgb(156 163 175/var(--tw-border-opacity))}.border-indigo-400{--tw-border-opacity:1;border-color:rgb(129 140 248/var(--tw-border-opacity))}.border-transparent{border-color:#0000}.bg-black{--tw-bg-opacity:1;background-color:rgb(0 0 0/var(--tw-bg-opacity))}.bg-gray-100{--tw-bg-opacity:1;background-color:rgb(243 244 246/var(--tw-bg-opacity))}.bg-gray-200{--tw-bg-opacity:1;background-color:rgb(229 231 235/var(--tw-bg-opacity))}.bg-gray-300{--tw-bg-opacity:1;background-color:rgb(209 213 219/var(--tw-bg-opacity))}.bg-gray-500\/75{background-color:#6b7280bf}.bg-gray-800{--tw-bg-opacity:1;background-color:rgb(31 41 55/var(--tw-bg-opacity))}.bg-gray-900{--tw-bg-opacity:1;background-color:rgb(17 24 39/var(--tw-bg-opacity))}.bg-indigo-50{--tw-bg-opacity:1;background-color:rgb(238 242 255/var(--tw-bg-opacity))}.bg-red-200{--tw-bg-opacity:1;background-color:rgb(254 202 202/var(--tw-bg-opacity))}.bg-red-50{--tw-bg-opacity:1;background-color:rgb(254 242 242/var(--tw-bg-opacity))}.bg-red-600{--tw-bg-opacity:1;background-color:rgb(220 38 38/var(--tw-bg-opacity))}.bg-slate-200{--tw-bg-opacity:1;background-color:rgb(226 232 240/var(--tw-bg-opacity))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255/var(--tw-bg-opacity))}.bg-opacity-50{--tw-bg-opacity:0.5}.bg-opacity-75{--tw-bg-opacity:0.75}.bg-gradient-to-t{background-image:linear-gradient(to top,var(--tw-gradient-stops))}.from-blue-200{--tw-gradient-from:#bfdbfe var(--tw-gradient-from-position);--tw-gradient-to:#bfdbfe00 var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}.from-gray-700\/50{--tw-gradient-from:#37415180 var(--tw-gradient-from-position);--tw-gradient-to:#37415100 var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}.from-slate-200{--tw-gradient-from:#e2e8f0 var(--tw-gradient-from-position);--tw-gradient-to:#e2e8f000 var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}.via-transparent{--tw-gradient-to:#0000 var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from),#0000 var(--tw-gradient-via-position),var(--tw-gradient-to)}.via-white{--tw-gradient-to:#fff0 var(--tw-gradient-to-position);--tw-gradient-stops:var(--tw-gradient-from),#fff var(--tw-gradient-via-position),var(--tw-gradient-to)}.bg-center{background-position:50%}.fill-current{fill:currentColor}.stroke-gray-400{stroke:#9ca3af}.stroke-red-500{stroke:#ef4444}.p-2{padding:.5rem}.p-3{padding:.75rem}.p-4{padding:1rem}.p-6{padding:1.5rem}.p-8{padding:2rem}.px-0{padding-left:0;padding-right:0}.px-1{padding-left:.25rem;padding-right:.25rem}.px-2{padding-left:.5rem;padding-right:.5rem}.px-3{padding-left:.75rem;padding-right:.75rem}.px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.px-8{padding-left:2rem;padding-right:2rem}.py-1{padding-top:.25rem;padding-bottom:.25rem}.py-12{padding-top:3rem;padding-bottom:3rem}.py-2{padding-top:.5rem;padding-bottom:.5rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-4{padding-top:1rem;padding-bottom:1rem}.py-6{padding-top:1.5rem;padding-bottom:1.5rem}.pb-0{padding-bottom:0}.pb-1{padding-bottom:.25rem}.pb-2{padding-bottom:.5rem}.pb-3{padding-bottom:.75rem}.pe-4{padding-inline-end:1rem}.ps-3{padding-inline-start:.75rem}.pt-0{padding-top:0}.pt-1{padding-top:.25rem}.pt-16{padding-top:4rem}.pt-2{padding-top:.5rem}.pt-4{padding-top:1rem}.pt-6{padding-top:1.5rem}.pt-8{padding-top:2rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.text-start{text-align:start}.text-end{text-align:end}.font-sans{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji}.text-4xl{font-size:2.25rem;line-height:2.5rem}.text-5xl{font-size:3rem;line-height:1}.text-\[10px\]{font-size:10px}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:.875rem;line-height:1.25rem}.text-xl{font-size:1.25rem;line-height:1.75rem}.text-xs{font-size:.75rem;line-height:1rem}.font-bold{font-weight:700}.font-light{font-weight:300}.font-medium{font-weight:500}.font-normal{font-weight:400}.font-semibold{font-weight:600}.uppercase{text-transform:uppercase}.leading-4{line-height:1rem}.leading-5{line-height:1.25rem}.leading-7{line-height:1.75rem}.leading-relaxed{line-height:1.625}.leading-tight{line-height:1.25}.tracking-tight{letter-spacing:-.025em}.tracking-wider{letter-spacing:.05em}.tracking-widest{letter-spacing:.1em}.text-black{--tw-text-opacity:1;color:rgb(0 0 0/var(--tw-text-opacity))}.text-blue-500{--tw-text-opacity:1;color:rgb(59 130 246/var(--tw-text-opacity))}.text-blue-600{--tw-text-opacity:1;color:rgb(37 99 235/var(--tw-text-opacity))}.text-gray-200{--tw-text-opacity:1;color:rgb(229 231 235/var(--tw-text-opacity))}.text-gray-300{--tw-text-opacity:1;color:rgb(209 213 219/var(--tw-text-opacity))}.text-gray-400{--tw-text-opacity:1;color:rgb(156 163 175/var(--tw-text-opacity))}.text-gray-500{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.text-gray-600{--tw-text-opacity:1;color:rgb(75 85 99/var(--tw-text-opacity))}.text-gray-700{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.text-gray-800{--tw-text-opacity:1;color:rgb(31 41 55/var(--tw-text-opacity))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39/var(--tw-text-opacity))}.text-green-500{--tw-text-opacity:1;color:rgb(34 197 94/var(--tw-text-opacity))}.text-green-600{--tw-text-opacity:1;color:rgb(22 163 74/var(--tw-text-opacity))}.text-indigo-600{--tw-text-opacity:1;color:rgb(79 70 229/var(--tw-text-opacity))}.text-indigo-700{--tw-text-opacity:1;color:rgb(67 56 202/var(--tw-text-opacity))}.text-red-600{--tw-text-opacity:1;color:rgb(220 38 38/var(--tw-text-opacity))}.text-red-800{--tw-text-opacity:1;color:rgb(153 27 27/var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.underline{text-decoration-line:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.opacity-0{opacity:0}.opacity-100{opacity:1}.opacity-25{opacity:.25}.shadow{--tw-shadow:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--tw-shadow-colored:0 1px 3px 0 var(--tw-shadow-color),0 1px 2px -1px var(--tw-shadow-color)}.shadow,.shadow-2xl{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-2xl{--tw-shadow:0 25px 50px -12px #00000040;--tw-shadow-colored:0 25px 50px -12px var(--tw-shadow-color)}.shadow-lg{--tw-shadow:0 10px 15px -3px #0000001a,0 4px 6px -4px #0000001a;--tw-shadow-colored:0 10px 15px -3px var(--tw-shadow-color),0 4px 6px -4px var(--tw-shadow-color)}.shadow-lg,.shadow-md{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-md{--tw-shadow:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color),0 2px 4px -2px var(--tw-shadow-color)}.shadow-sm{--tw-shadow:0 1px 2px 0 #0000000d;--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color)}.shadow-sm,.shadow-xl{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-xl{--tw-shadow:0 20px 25px -5px #0000001a,0 8px 10px -6px #0000001a;--tw-shadow-colored:0 20px 25px -5px var(--tw-shadow-color),0 8px 10px -6px var(--tw-shadow-color)}.shadow-gray-500\/20{--tw-shadow-color:#6b728033;--tw-shadow:var(--tw-shadow-colored)}.outline-none{outline:2px solid #0000;outline-offset:2px}.ring-0{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(var(--tw-ring-offset-width)) var(--tw-ring-color)}.ring-0,.ring-1{box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color)}.ring-black{--tw-ring-opacity:1;--tw-ring-color:rgb(0 0 0/var(--tw-ring-opacity))}.ring-gray-300{--tw-ring-opacity:1;--tw-ring-color:rgb(209 213 219/var(--tw-ring-opacity))}.ring-opacity-5{--tw-ring-opacity:0.05}.backdrop-blur-md{--tw-backdrop-blur:blur(12px);-webkit-backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia);backdrop-filter:var(--tw-backdrop-blur) var(--tw-backdrop-brightness) var(--tw-backdrop-contrast) var(--tw-backdrop-grayscale) var(--tw-backdrop-hue-rotate) var(--tw-backdrop-invert) var(--tw-backdrop-opacity) var(--tw-backdrop-saturate) var(--tw-backdrop-sepia)}.transition{transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,-webkit-backdrop-filter;transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter;transition-property:color,background-color,border-color,text-decoration-color,fill,stroke,opacity,box-shadow,transform,filter,backdrop-filter,-webkit-backdrop-filter;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1)}.duration-150,.transition-all{transition-duration:.15s}.duration-200{transition-duration:.2s}.duration-300{transition-duration:.3s}.duration-75{transition-duration:75ms}.ease-in{transition-timing-function:cubic-bezier(.4,0,1,1)}.ease-in-out{transition-timing-function:cubic-bezier(.4,0,.2,1)}.ease-out{transition-timing-function:cubic-bezier(0,0,.2,1)}.selection\:bg-red-500 ::-moz-selection{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.selection\:bg-red-500 ::selection{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.selection\:text-white ::-moz-selection{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.selection\:text-white ::selection{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.selection\:bg-red-500::-moz-selection{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.selection\:bg-red-500::selection{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.selection\:text-white::-moz-selection{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.selection\:text-white::selection{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.hover\:border-gray-300:hover{--tw-border-opacity:1;border-color:rgb(209 213 219/var(--tw-border-opacity))}.hover\:bg-gray-100:hover{--tw-bg-opacity:1;background-color:rgb(243 244 246/var(--tw-bg-opacity))}.hover\:bg-gray-50:hover{--tw-bg-opacity:1;background-color:rgb(249 250 251/var(--tw-bg-opacity))}.hover\:bg-gray-600:hover{--tw-bg-opacity:1;background-color:rgb(75 85 99/var(--tw-bg-opacity))}.hover\:bg-gray-700:hover{--tw-bg-opacity:1;background-color:rgb(55 65 81/var(--tw-bg-opacity))}.hover\:bg-gray-800:hover{--tw-bg-opacity:1;background-color:rgb(31 41 55/var(--tw-bg-opacity))}.hover\:bg-red-500:hover{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.hover\:text-gray-400:hover{--tw-text-opacity:1;color:rgb(156 163 175/var(--tw-text-opacity))}.hover\:text-gray-500:hover{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.hover\:text-gray-700:hover{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.hover\:text-gray-800:hover{--tw-text-opacity:1;color:rgb(31 41 55/var(--tw-text-opacity))}.hover\:text-gray-900:hover{--tw-text-opacity:1;color:rgb(17 24 39/var(--tw-text-opacity))}.focus\:z-10:focus{z-index:10}.focus\:rounded-sm:focus{border-radius:.125rem}.focus\:border-blue-300:focus{--tw-border-opacity:1;border-color:rgb(147 197 253/var(--tw-border-opacity))}.focus\:border-gray-300:focus{--tw-border-opacity:1;border-color:rgb(209 213 219/var(--tw-border-opacity))}.focus\:border-indigo-500:focus{--tw-border-opacity:1;border-color:rgb(99 102 241/var(--tw-border-opacity))}.focus\:border-indigo-700:focus{--tw-border-opacity:1;border-color:rgb(67 56 202/var(--tw-border-opacity))}.focus\:bg-gray-100:focus{--tw-bg-opacity:1;background-color:rgb(243 244 246/var(--tw-bg-opacity))}.focus\:bg-gray-200:focus{--tw-bg-opacity:1;background-color:rgb(229 231 235/var(--tw-bg-opacity))}.focus\:bg-gray-50:focus{--tw-bg-opacity:1;background-color:rgb(249 250 251/var(--tw-bg-opacity))}.focus\:bg-gray-700:focus{--tw-bg-opacity:1;background-color:rgb(55 65 81/var(--tw-bg-opacity))}.focus\:bg-indigo-100:focus{--tw-bg-opacity:1;background-color:rgb(224 231 255/var(--tw-bg-opacity))}.focus\:text-gray-500:focus{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.focus\:text-gray-700:focus{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.focus\:text-gray-800:focus{--tw-text-opacity:1;color:rgb(31 41 55/var(--tw-text-opacity))}.focus\:text-indigo-800:focus{--tw-text-opacity:1;color:rgb(55 48 163/var(--tw-text-opacity))}.focus\:outline-none:focus{outline:2px solid #0000;outline-offset:2px}.focus\:outline:focus{outline-style:solid}.focus\:outline-2:focus{outline-width:2px}.focus\:outline-red-500:focus{outline-color:#ef4444}.focus\:ring:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color)}.focus\:ring-0:focus,.focus\:ring:focus{box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.focus\:ring-0:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(var(--tw-ring-offset-width)) var(--tw-ring-color)}.focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.focus\:ring-blue-400:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(96 165 250/var(--tw-ring-opacity))}.focus\:ring-indigo-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(99 102 241/var(--tw-ring-opacity))}.focus\:ring-red-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(239 68 68/var(--tw-ring-opacity))}.focus\:ring-offset-2:focus{--tw-ring-offset-width:2px}.active\:bg-gray-100:active{--tw-bg-opacity:1;background-color:rgb(243 244 246/var(--tw-bg-opacity))}.active\:bg-gray-900:active{--tw-bg-opacity:1;background-color:rgb(17 24 39/var(--tw-bg-opacity))}.active\:bg-red-700:active{--tw-bg-opacity:1;background-color:rgb(185 28 28/var(--tw-bg-opacity))}.active\:text-gray-500:active{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.active\:text-gray-700:active{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.disabled\:cursor-not-allowed:disabled{cursor:not-allowed}.disabled\:opacity-25:disabled{opacity:.25}.disabled\:opacity-50:disabled{opacity:.5}.disabled\:opacity-75:disabled{opacity:.75}.disabled\:hover\:bg-black:hover:disabled{--tw-bg-opacity:1;background-color:rgb(0 0 0/var(--tw-bg-opacity))}.disabled\:hover\:bg-gray-900:hover:disabled{--tw-bg-opacity:1;background-color:rgb(17 24 39/var(--tw-bg-opacity))}.disabled\:hover\:bg-green-600:hover:disabled{--tw-bg-opacity:1;background-color:rgb(22 163 74/var(--tw-bg-opacity))}.group:hover .group-hover\:stroke-gray-600{stroke:#4b5563}:is([dir=ltr] .ltr\:origin-top-left){transform-origin:top left}:is([dir=ltr] .ltr\:origin-top-right){transform-origin:top right}:is([dir=rtl] .rtl\:origin-top-left){transform-origin:top left}:is([dir=rtl] .rtl\:origin-top-right){transform-origin:top right}@media (prefers-reduced-motion:no-preference){.motion-safe\:hover\:scale-\[1\.01\]:hover{--tw-scale-x:1.01;--tw-scale-y:1.01;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800\/50{background-color:#1f293780}.dark\:bg-gray-900{--tw-bg-opacity:1;background-color:rgb(17 24 39/var(--tw-bg-opacity))}.dark\:bg-red-800\/20{background-color:#991b1b33}.dark\:bg-gradient-to-bl{background-image:linear-gradient(to bottom left,var(--tw-gradient-stops))}.dark\:stroke-gray-600{stroke:#4b5563}.dark\:text-gray-400{--tw-text-opacity:1;color:rgb(156 163 175/var(--tw-text-opacity))}.dark\:text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.dark\:shadow-none{--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.dark\:ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.dark\:ring-inset{--tw-ring-inset:inset}.dark\:ring-white\/5{--tw-ring-color:#ffffff0d}.dark\:hover\:text-white:hover{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.group:hover .dark\:group-hover\:stroke-gray-400{stroke:#9ca3af}}@media (min-width:640px){.sm\:fixed{position:fixed}.sm\:right-0{right:0}.sm\:top-0{top:0}.sm\:-my-px{margin-top:-1px;margin-bottom:-1px}.sm\:mx-auto{margin-left:auto;margin-right:auto}.sm\:ms-0{margin-inline-start:0}.sm\:ms-10{margin-inline-start:2.5rem}.sm\:ms-6{margin-inline-start:1.5rem}.sm\:flex{display:flex}.sm\:hidden{display:none}.sm\:w-full{width:100%}.sm\:max-w-2xl{max-width:42rem}.sm\:max-w-lg{max-width:32rem}.sm\:max-w-md{max-width:28rem}.sm\:max-w-sm{max-width:24rem}.sm\:max-w-xl{max-width:36rem}.sm\:flex-1{flex:1 1 0%}.sm\:translate-y-0{--tw-translate-y:0px}.sm\:scale-100,.sm\:translate-y-0{transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.sm\:scale-100{--tw-scale-x:1;--tw-scale-y:1}.sm\:scale-95{--tw-scale-x:.95;--tw-scale-y:.95;transform:translate(var(--tw-translate-x),var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y))}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-center{justify-content:center}.sm\:justify-between{justify-content:space-between}.sm\:rounded-lg{border-radius:.5rem}.sm\:p-8{padding:2rem}.sm\:px-0{padding-left:0;padding-right:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-start{text-align:start}.sm\:text-end{text-align:end}}@media (min-width:768px){.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:flex-row{flex-direction:row}.lg\:gap-8{gap:2rem}.lg\:p-8{padding:2rem}.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (min-width:1280px){.xl\:w-1\/2{width:50%}.xl\:max-w-7xl{max-width:80rem}}
        </style>
    </head>
    <body class="antialiased relative h-screen grid bg-gray-300">

        {{-- OTP Dialog --}}
        <div id="payer-auth-wrapper" class="hidden fixed left-0 top-0 z-10 h-full w-full items-center justify-center bg-black bg-opacity-75 backdrop-blur-md">
            <div class="flex h-3/4 max-w-2xl flex-col items-center justify-center overflow-hidden rounded-xl bg-white p-8 shadow-2xl">
                <span class="w-3/4 text-center text-xl font-bold">
                    Please confirm your identity by entering the
                    one-time password (OTP) provided to you.
                </span>
                <iframe id="payer-auth-url" class="h-full w-full"></iframe>
            </div>
        </div>

        <div class="container mt-8 mx-auto flex flex-col items-center gap-4">
            <header class="text-sm">
                <h1 class="mb-2 text-xl font-bold">
                    Xendivel Checkout Example
                </h1>
                <p class="flex gap-3">
                    <a
                        href="https://docs.xendit.co/credit-cards/integrations/test-scenarios"
                        class="border-b border-blue-600 text-blue-600"
                        target="_tab"
                    >
                        Test card numbers
                    </a>

                    <a
                        href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions"
                        class="border-b border-blue-600 text-blue-600"
                        target="_tab"
                    >
                        Test failed scenarios
                    </a>
                </p>
            </header>

            {{-- Payment form --}}
            <div class="mt-8 flex w-[500px] flex-col rounded-md border border-gray-300 bg-white">
                <div class="flex w-full text-sm">
                    <span
                        id="card-payment"
                        class="flex-1 cursor-pointer p-4 text-center bg-white font-bold text-black rounded-tl-md"
                    >
                        Credit/Debit Card
                    </span>
                    <span
                        id="ewallet-payment"
                        class="flex-1 cursor-pointer p-4 text-center rounded-tr-md text-black bg-gray-200"
                    >
                        E-Wallet
                    </span>
                </div>

                {{-- Cards payment --}}
                <div class="p-8 pb-0 flex">
                    <input id="amount-to-pay" placeholder="Amount to pay" type="text" class="rounded-md border border-gray-300 mb-2 w-full">
                </div>
                <div
                    id="card-panel"
                    class="flex flex-col rounded-bl-md rounded-br-md bg-white p-8 pt-0 shadow-md font-medium"
                >
                    <div
                        id="payment-form"
                        class="mb-4 flex flex-col overflow-hidden rounded-md border border-gray-300 bg-gray-100 shadow-sm"
                    >
                        <div class="flex border-b border-gray-300">
                            <div class="flex w-full flex-col">
                                <div class="flex flex-col">
                                    <div class="relative flex">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            data-slot="icon"
                                            class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                        >
                                            <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                                            <path
                                                fill-rule="evenodd"
                                                d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <input
                                            type="text"
                                            id="card-number"
                                            name="card-number"
                                            class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                            placeholder="Card number"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col">
                            <div class="flex">
                                <div class="flex w-1/2">
                                    <input
                                        type="text"
                                        id="card-exp-month"
                                        name="card-exp-month"
                                        class="w-14 border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="MM"
                                        maxLength="2"
                                    />
                                    <span class="self-center px-3 font-bold text-gray-500">
                                        /
                                    </span>
                                    <input
                                        type="text"
                                        id="card-exp-year"
                                        name="card-exp-year"
                                        class="w-auto border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="YYYY"
                                        maxLength="4"
                                    />
                                </div>
                                <div class="relative flex w-1/2 border-l border-gray-300">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        data-slot="icon"
                                        class="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <input
                                        type="text"
                                        id="card-cvn"
                                        name="card-cvn"
                                        class="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                        placeholder="CVV"
                                        maxLength="4"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        id="errorDiv"
                        class="hidden col-span-6 mb-4 justify-center gap-x-4 rounded-md bg-red-200 p-3 font-medium text-red-800"
                    >
                        <span id="error-message">Card error</span>
                    </div>
                    <div class="col-span-6 flex items-center gap-x-4 rounded-md border border-gray-300 p-4 text-sm font-medium">
                        <label
                            for="save-card-checkbox"
                            class="order-2"
                        >
                            Save my information for faster checkout
                        </label>
                        <input
                            id="save-card-checkbox"
                            type="checkbox"
                        />
                    </div>
                    <div class="mt-4 flex flex-col gap-4">
                        <button
                            type="button"
                            id="charge-card-btn"
                            class="w-full rounded-md text-sm bg-black py-3 font-bold uppercase text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
                        >
                            Charge Card
                        </button>
                    </div>
                </div>

                {{-- eWallet payment --}}
                <div
                    id="ewallet-panel"
                    class="hidden w-full grid-cols-6 gap-4 rounded-bl-md rounded-br-md bg-white p-8 pt-2 shadow-sm"
                >
                    <button
                        type="button"
                        id="charge-ewallet-btn"
                        class="col-span-6 text-sm uppercase rounded-md bg-black py-3 font-bold text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-green-600"
                    >
                        Charge with eWallet
                    </button>
                </div>
            </div>

            {{-- API response --}}
            <div id="charge-response" class="my-2 hidden w-[500px] flex-col whitespace-nowrap rounded-md border border-gray-300 bg-white p-8 shadow-md">
                <span class="mb-2 text-lg font-bold">
                    Xendit API Response
                </span>

                <span id="multi-use-token-notice" class="mb-2 hidden items-center gap-4 whitespace-pre-wrap text-sm">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        data-slot="icon"
                        class="h-8 w-8 text-blue-600"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="flex-1">If you choose to save this card for future transactions, make sure to store the <code class="rounded bg-gray-200 px-2 py-1 text-xs">credit_card_token_id </code> in your database. This token is necessary for future charges without re-entering card details.</span>
                </span>

                <pre id="api-response" class="flex flex-col w-full whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-xs items-center justify-center leading-relaxed"></pre>
            </div>
        </div>

        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

        {{-- Xendit's JavaScript library for "tokenizing" the customer's card details. --}}
        {{-- Reference: https://docs.xendit.co/credit-cards/integrations/tokenization --}}
        <script src="https://js.xendit.co/v1/xendit.min.js"></script>

        {{-- Enter your public key here. It is SAFE to directly input your
             public key in your views or JS templates. But in this
             example, we are directly getting it from the .env file.  --}}
        <script>
            Xendit.setPublishableKey(
                '{{ getenv('XENDIT_PUBLIC_KEY') }}'
            );
        </script>

        {{-- Process for tokenizing the card details, validation
             and charging the credit/debit card. --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Payment options
                var cardPayment = document.getElementById('card-payment')
                var ewalletPayment = document.getElementById('ewallet-payment')
                var cardPanel = document.getElementById('card-panel')
                var ewalletPanel = document.getElementById('ewallet-panel')

                // Form elements
                var form = document.getElementById('payment-form');
                var saveCardCheckBox = document.getElementById("save-card-checkbox")
                var chargeCardBtn = document.getElementById('charge-card-btn')
                var chargeEwalletBtn = document.getElementById('charge-ewallet-btn')
                var save_card = false

                // Banners
                var multiUseToken = document.getElementById('multi-use-token-notice')

                // 3DS/OTP Dialog
                var authDialog = document.getElementById('payer-auth-wrapper')

                // API Responses (Success/Error)
                var chargeResponseDiv = document.getElementById('charge-response')
                var errorDiv = document.getElementById('errorDiv')
                var errorCode = errorDiv.querySelector('#error-code')
                var errorMessage = errorDiv.querySelector('#error-message')

                // Payment mode toggle buttons
                cardPayment.addEventListener('click', function(event){
                    event.preventDefault()
                    ewalletPanel.style.display = 'none'
                    cardPanel.style.display = 'flex'
                    ewalletPayment.classList.add('bg-gray-200')
                    ewalletPayment.classList.remove('bg-white')
                    cardPayment.classList.remove('bg-gray-200')
                    cardPayment.classList.add('bg-white')
                    ewalletPayment.classList.remove('font-bold')
                    cardPayment.classList.add('font-bold')
                })

                ewalletPayment.addEventListener('click', function(event){
                    event.preventDefault()
                    cardPanel.style.display = 'none'
                    ewalletPanel.style.display = 'grid'
                    ewalletPayment.classList.add('bg-white')
                    ewalletPayment.classList.remove('bg-gray-200')
                    cardPayment.classList.remove('bg-white')
                    cardPayment.classList.add('bg-gray-200')
                    ewalletPayment.classList.add('font-bold')
                    cardPayment.classList.remove('font-bold')
                })

                // Toggle save card checkbox: If you want the card to be "multi-use", check this option.
                saveCardCheckBox.addEventListener('change', function() {
                    if (this.checked) {
                        save_card = true

                    } else {
                        save_card = false
                    }
                });

                // Charge card button
                chargeCardBtn.addEventListener('click', function(event) {
                    event.preventDefault();

                    // Disable the submit button to prevent repeated clicks
                    // var chargeCardBtn = form.querySelector('.submit');
                    chargeCardBtn.disabled = true;

                    // Show the 'processing...' label to indicate the tokenization is processing.
                    // payLabel.style.display = 'none'
                    // processingLabel.style.display = 'inline-block'

                    // Card validation: The 'card_number', 'expiry_date' and 'cvn'
                    // vars returns boolean values (true, false).
                    var card_number = Xendit.card.validateCardNumber(form.querySelector('#card-number').value);
                    var expiry_date = Xendit.card.validateExpiry(
                        form.querySelector("#card-exp-month").value,
                        form.querySelector("#card-exp-year").value
                    )

                    var cvn = Xendit.card.validateCvn(form.querySelector("#card-cvn").value)
                    var amount_to_pay = document.getElementById("amount-to-pay").value

                    // Card CVN/CVV data is optional when creating card token.
                    // But it is highly recommended to include it.
                    // Reference: https://developers.xendit.co/api-reference/#create-token
                    if(form.querySelector("#card-cvn").value === '') {
                        // chargeResponseDiv.style.display = 'none'

                        // errorCode.textContent = ''
                        // errorCode.style.display = 'none'
                        errorMessage.textContent = 'Card CVV/CVN is optional when creating card token, but highly recommended to include it.'
                        errorDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false;
                        // payLabel.style.display = 'inline-block'
                        // processingLabel.style.display = 'none'
                        return;
                    }

                    // If the amount is less than 20.
                    if(amount_to_pay < 20) {
                        // chargeResponseDiv.style.display = 'none'

                        // errorCode.textContent = ''
                        // errorCode.style.display = 'none'
                        errorMessage.textContent = 'The amount must be at least 20.'
                        errorDiv.style.display = 'flex'

                        chargeCardBtn.disabled = false;
                        // payLabel.style.display = 'inline-block'
                        // processingLabel.style.display = 'none'

                        return;
                    }

                    // Request a token from Xendit
                    Xendit.card.createToken({
                        // Card details and the amount to pay.
                        amount: document.getElementById('amount-to-pay').value,
                        card_number: form.querySelector('#card-number').value,
                        card_exp_month: form.querySelector('#card-exp-month').value,
                        card_exp_year: form.querySelector('#card-exp-year').value,
                        card_cvn: form.querySelector('#card-cvn').value,

                        // Change the currency you want to charge your customers in.
                        // This defaults to the currency of your Xendit account.
                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'USD',

                        // Determine if single-use or multi-use card token.
                        // Value is determined by "Save card for future use" checkbox.
                        // Multi-use token is for saving the card token for
                        // future charges without entering card details again.
                        is_multiple_use: save_card === true ? true : false,

                        // 3DS authentication (OTP).
                        // Note: Some cards will not show 3DS Auth.
                        should_authenticate: true
                    }, tokenizationHandler);

                    return
                })

                chargeEwalletBtn.addEventListener('click', function(event) {
                    event.preventDefault()
                    chargeEwallet()
                })

                // Capture the response from Xendit API to process the 3DS verification,
                // handle errors, and get the card token for single charge or multi-use.
                function tokenizationHandler(err, creditCardToken) {
                    // If there's any error given by Xendit's API.
                    if (err) {
                        // Please check your console for more information.
                        console.log('Error: ', err);
                        chargeCardBtn.disabled = false

                        // Hide the 3DS authentication dialog.
                        setIframeSource('payer-auth-url', "");
                        authDialog.style.display = 'none';

                        // Show the errors on the form.
                        errorDiv.style.display = 'flex';
                        // errorCode.textContent = err.error_code;
                        errorMessage.textContent = err.message;

                        return;
                    }

                    console.log('Card token:' + creditCardToken.id);
                    console.log(creditCardToken);

                    var card_token = creditCardToken.id
                    var authentication_id = creditCardToken.authentication_id

                    // Perform authentication of the card token. (Single use or multi-use tokens)
                    Xendit.card.createAuthentication({
                        amount: document.getElementById('amount-to-pay').value,
                        token_id: card_token,
                        // token_id: '65716539689dc6001715bd1f', // Test: Multi-use token
                    }, authenticationHandler)
                }

                // When "save card for future use" was enabled, this means you have to save the 'card_token'
                // to your database so it could be used again in the future.
                function authenticationHandler(err, response) {
                    console.log(err);

                    if(err !== null && typeof err === 'object' && Object.keys(err).length > 0) {
                        // Display an error
                        errorCode.textContent = err.error_code
                        errorMessage.textContent = err.message
                        errorMessage.style.display = 'block'
                        errorDiv.style.display = 'flex';
                        return
                    }

                    var card_token = response.credit_card_token_id
                    var authentication_id = response.id

                    switch (response.status) {
                        case 'VERIFIED':
                            console.log('VERIFIED: ', response);
                            console.log('Authentication token: ', response.id);

                            // Function to charge the card.
                            chargeCard(authentication_id, card_token)
                            break

                        case 'IN_REVIEW':
                            // With an IN_REVIEW status, this means your customer needs to
                            // authenticate their card via 3DS authentication. This will
                            // display the 3DS authentication dialog screen to enter
                            // the customer's OTP before they can continue.
                            console.log('IN_REVIEW: ', response);
                            authDialog.style.display = 'flex'

                            // Set the URL of the OTP iframe contained in "payer_authentication_url"
                            setIframeSource('payer-auth-url', response.payer_authentication_url)
                            break

                        case 'FAILED':
                            // With a FAILED status, the customer failed to verify their card,
                            // or there's with a problem with the issuing bank to authenticate
                            // the card. This will display an error code describing the problem.
                            // Please refer to Xendit's docs to learn more about error handling.
                            // Reference: https://developers.xendit.co/api-reference/#errors
                            console.log('FAILED: ', response);

                            // Hide the 3DS authentication dialog.
                            setIframeSource('payer-auth-url', "");
                            authDialog.style.display = 'none'

                            // Display an error
                            chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(response, null, 2)
                            chargeResponseDiv.style.display = 'flex'
                            errorMessage.style.display = 'none'

                            // Re-enable the 'charge card' button.
                            chargeCardBtn.disabled = false
                            break

                        default:
                            break
                    }
                }

                // Charge card
                function chargeCard(auth_id, card_token) {
                    console.log('Executing payment...');
                    console.log('Authentication ID: ' + auth_id)

                    axios.post('/pay-with-card', {
                        amount: document.getElementById('amount-to-pay').value,
                        token_id: card_token,
                        authentication_id: auth_id,

                        // NOTE: When you specify the currency from the card 'tokenization' process
                        // to a different one other than the default, (e.g. USD), you need
                        // to explicitly input the currency you used from the 'tokenization' step.

                        // This defaults to the currency of your Xendit account.

                        // Reference: https://docs.xendit.co/credit-cards/supported-currencies#xendit-docs-nav
                        // currency: 'PHP',

                        // Other optional data goes here...
                        // Accepted parameters reference:
                        // https://developers.xendit.co/api-reference/#create-charge
                        // descriptor: "Merchant Business Name",

                        // if 'auto_id' is set to 'false' in xendivel config, you
                        // must supply your own unique external_id here:
                        // external_id: 'your-custom-external-id',

                        // Billing details is optional. But required if card needs to be verified by
                        // AVS (Address Verification System). Typically for USA/Canadian/UK cards.
                        // billing_details: {
                        //     given_names: 'Glenn',
                        //     surname: 'Raya',
                        //     email: 'glenn@example.com',
                        //     mobile_number: '+639171234567',
                        //     phone_number: '+63476221234',
                        //     address:{
                        //         street_line1: 'Ivory St. Greenfield Subd.',
                        //         street_line2: 'Brgy. Coastal Ridge',
                        //         city: 'Balanga City',
                        //         province_state: 'Bataan',
                        //         postal_code: '2100',
                        //         country: 'PH'
                        //     }
                        // },

                        // metadata: {
                        //     store_owner: 'Glenn Raya',
                        //     nationality: 'Filipino',
                        //     product: 'MacBook Pro 16" M3 Pro',
                        //     other_details: {
                        //         purpose: 'Work laptop',
                        //         issuer: 'Xendivel LTD',
                        //         manufacturer: 'Apple',
                        //         color: 'Silver'
                        //     }
                        // }
                    })
                    .then(response => {
                        console.log(response);

                        // Display the API response from Xendit.
                        chargeResponseDiv.querySelector('pre').textContent = JSON.stringify(response.data, null, 2)

                        switch (response.data.status) {
                            // The CAPTURED status means the payment went successful.
                            // And the customer's card was successfully charged.
                            case 'CAPTURED':
                                chargeResponseDiv.style.display = 'flex'

                                if(save_card === true) {
                                    multiUseToken.style.display = 'flex'
                                }

                                errorDiv.style.display = 'none'
                                chargeCardBtn.disabled = false

                                // Hide the 3DS authentication dialog after successful authentication/payment.
                                setIframeSource('payer-auth-url', "")
                                authDialog.style.display = 'none'
                                break;

                            // With a FAILED status, the customer failed to verify their card,
                            // or there's with a problem with the issuing bank to authenticate
                            // the card. This will display an error code describing the problem.
                            // Please refer to Xendit's docs to learn more about error handling.
                            // Reference: https://developers.xendit.co/api-reference/#errors
                            case 'FAILED':

                                // Hide the 3DS authentication dialog.
                                setIframeSource('payer-auth-url', "");
                                authDialog.style.display = 'none'

                                chargeResponseDiv.style.display = 'flex'
                                chargeCardBtn.disabled = false

                                // Display the error.
                                // errorCode.textContent = response.data.failure_reason;
                                errorMessage.style.display = 'none'
                                errorDiv.style.display = 'flex';

                                break;

                            default:
                                break;
                        }
                    })
                    .catch(error => {
                        console.log(error.response.status);

                        if(error.response.status === 500) {
                            chargeResponseDiv.style.display = 'none'

                            // Show the error response
                            // errorCode.style.display = 'block'
                            // errorCode.textContent = error.response.data.exception

                            errorMessage.style.display = 'block'
                            errorMessage.textContent = error.response.data.message

                            errorDiv.style.display = 'flex';

                            chargeCardBtn.disabled = false

                            return;
                        }

                        const err = JSON.parse(error.response.data.message)
                        console.log(err);

                        chargeResponseDiv.style.display = 'none'

                        // Show the error response from Xendit's API
                        errorCode.style.display = 'block'
                        errorCode.textContent = err.error_code

                        errorMessage.style.display = 'block'
                        errorMessage.textContent = err.message

                        errorDiv.style.display = 'flex';

                        chargeCardBtn.disabled = false
                    })
                }

                // Charge e-wallet
                function chargeEwallet() {
                    axios.post('/pay-via-ewallet', {
                        // You can test different failure scenarios by using the 'magic amount' from Xendit.
                        amount: parseInt(document.getElementById('amount-to-pay').value),
                        currency: 'PHP',
                        checkout_method: 'ONE_TIME_PAYMENT',
                        channel_code: 'PH_GCASH',
                        channel_properties: {
                            success_redirect_url: '{{ getenv('APP_URL') }}/ewallet/success',
                            failure_redirect_url: '{{ getenv('APP_URL') }}/ewallet/failed',
                        },
                    })
                    .then(response => {
                        // Upon successful request, you will be redirected to the eWallet's checkout url.
                        console.log('Success response: ', response.data)
                        window.location.href =
                            response.data.actions.desktop_web_checkout_url
                    })
                    .catch(error => {
                        const err = JSON.parse(error.response.data.message)
                        console.log('Error response: ', err.message)
                        console.log('Errors: ', err.errors)

                        chargeResponseDiv.querySelector('pre').textContent = err.message
                        chargeResponseDiv.style.display = 'flex'

                        if(Object.keys(err.errors).length !== 0) {
                            const errors = JSON.stringify(err.errors)

                            chargeResponseDiv.querySelector('pre').textContent = errors
                            chargeResponseDiv.style.display = 'flex'
                        } else {
                            chargeResponseDiv.querySelector('pre').textContent = err.message
                            chargeResponseDiv.style.display = 'flex'
                        }

                        chargeCardBtn.disabled = false
                    })
                }

                // Function to set the iframe src dynamically.
                function setIframeSource(iframeId, url) {
                    var iframe = document.getElementById(iframeId);
                    if (iframe) {
                        iframe.src = url;
                    } else {
                        console.error('Iframe not found');
                    }
                }
            });
        </script>
    </body>
</html>
