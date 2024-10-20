<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice }}</title>
    <style>
        *,
        ::before,
        ::after {
        box-sizing: border-box;
        /* 1 */
        border-width: 0;
        /* 2 */
        border-style: solid;
        /* 2 */
        border-color: #e5e7eb;
        /* 2 */
        }

        ::before,
        ::after {
        --tw-content: '';
        }

        /*
        1. Use a consistent sensible line-height in all browsers.
        2. Prevent adjustments of font size after orientation changes in iOS.
        3. Use a more readable tab size.
        4. Use the user's configured `sans` font-family by default.
        5. Use the user's configured `sans` font-feature-settings by default.
        6. Use the user's configured `sans` font-variation-settings by default.
        */

        html {
        line-height: 1.5;
        /* 1 */
        -webkit-text-size-adjust: 100%;
        /* 2 */
        -moz-tab-size: 4;
        /* 3 */
        tab-size: 4;
        /* 3 */
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        /* 4 */
        font-feature-settings: normal;
        /* 5 */
        font-variation-settings: normal;
        /* 6 */
        }

        /*
        1. Remove the margin in all browsers.
        2. Inherit line-height from `html` so users can set them as a class directly on the `html` element.
        */

        body {
        margin: 0;
        /* 1 */
        line-height: inherit;
        /* 2 */
        }

        /*
        1. Add the correct height in Firefox.
        2. Correct the inheritance of border color in Firefox. (https://bugzilla.mozilla.org/show_bug.cgi?id=190655)
        3. Ensure horizontal rules are visible by default.
        */

        hr {
        height: 0;
        /* 1 */
        color: inherit;
        /* 2 */
        border-top-width: 1px;
        /* 3 */
        }

        /*
        Add the correct text decoration in Chrome, Edge, and Safari.
        */

        abbr:where([title]) {
        -webkit-text-decoration: underline dotted;
                text-decoration: underline dotted;
        }

        /*
        Remove the default font size and weight for headings.
        */

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
        font-size: inherit;
        font-weight: inherit;
        }

        /*
        Reset links to optimize for opt-in styling instead of opt-out.
        */

        a {
        color: inherit;
        text-decoration: inherit;
        }

        /*
        Add the correct font weight in Edge and Safari.
        */

        b,
        strong {
        font-weight: bolder;
        }

        /*
        1. Use the user's configured `mono` font family by default.
        2. Correct the odd `em` font sizing in all browsers.
        */

        code,
        kbd,
        samp,
        pre {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        /* 1 */
        font-size: 1em;
        /* 2 */
        }

        /*
        Add the correct font size in all browsers.
        */

        small {
        font-size: 80%;
        }

        /*
        Prevent `sub` and `sup` elements from affecting the line height in all browsers.
        */

        sub,
        sup {
        font-size: 75%;
        line-height: 0;
        position: relative;
        vertical-align: baseline;
        }

        sub {
        bottom: -0.25em;
        }

        sup {
        top: -0.5em;
        }

        /*
        1. Remove text indentation from table contents in Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=999088, https://bugs.webkit.org/show_bug.cgi?id=201297)
        2. Correct table border color inheritance in all Chrome and Safari. (https://bugs.chromium.org/p/chromium/issues/detail?id=935729, https://bugs.webkit.org/show_bug.cgi?id=195016)
        3. Remove gaps between table borders by default.
        */

        table {
        text-indent: 0;
        /* 1 */
        border-color: inherit;
        /* 2 */
        border-collapse: collapse;
        /* 3 */
        }

        /*
        1. Change the font styles in all browsers.
        2. Remove the margin in Firefox and Safari.
        3. Remove default padding in all browsers.
        */

        button,
        input,
        optgroup,
        select,
        textarea {
        font-family: inherit;
        /* 1 */
        font-feature-settings: inherit;
        /* 1 */
        font-variation-settings: inherit;
        /* 1 */
        font-size: 100%;
        /* 1 */
        font-weight: inherit;
        /* 1 */
        line-height: inherit;
        /* 1 */
        color: inherit;
        /* 1 */
        margin: 0;
        /* 2 */
        padding: 0;
        /* 3 */
        }

        /*
        Remove the inheritance of text transform in Edge and Firefox.
        */

        button,
        select {
        text-transform: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Remove default button styles.
        */

        button,
        [type='button'],
        [type='reset'],
        [type='submit'] {
        -webkit-appearance: button;
        /* 1 */
        background-color: transparent;
        /* 2 */
        background-image: none;
        /* 2 */
        }

        /*
        Use the modern Firefox focus style for all focusable elements.
        */

        :-moz-focusring {
        outline: auto;
        }

        /*
        Remove the additional `:invalid` styles in Firefox. (https://github.com/mozilla/gecko-dev/blob/2f9eacd9d3d995c937b4251a5557d95d494c9be1/layout/style/res/forms.css#L728-L737)
        */

        :-moz-ui-invalid {
        box-shadow: none;
        }

        /*
        Add the correct vertical alignment in Chrome and Firefox.
        */

        progress {
        vertical-align: baseline;
        }

        /*
        Correct the cursor style of increment and decrement buttons in Safari.
        */

        ::-webkit-inner-spin-button,
        ::-webkit-outer-spin-button {
        height: auto;
        }

        /*
        1. Correct the odd appearance in Chrome and Safari.
        2. Correct the outline style in Safari.
        */

        [type='search'] {
        -webkit-appearance: textfield;
        /* 1 */
        outline-offset: -2px;
        /* 2 */
        }

        /*
        Remove the inner padding in Chrome and Safari on macOS.
        */

        ::-webkit-search-decoration {
        -webkit-appearance: none;
        }

        /*
        1. Correct the inability to style clickable types in iOS and Safari.
        2. Change font properties to `inherit` in Safari.
        */

        ::-webkit-file-upload-button {
        -webkit-appearance: button;
        /* 1 */
        font: inherit;
        /* 2 */
        }

        /*
        Add the correct display in Chrome and Safari.
        */

        summary {
        display: list-item;
        }

        /*
        Removes the default spacing and border for appropriate elements.
        */

        blockquote,
        dl,
        dd,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        figure,
        p,
        pre {
        margin: 0;
        }

        fieldset {
        margin: 0;
        padding: 0;
        }

        legend {
        padding: 0;
        }

        ol,
        ul,
        menu {
        list-style: none;
        margin: 0;
        padding: 0;
        }

        /*
        Reset default styling for dialogs.
        */

        dialog {
        padding: 0;
        }

        /*
        Prevent resizing textareas horizontally by default.
        */

        textarea {
        resize: vertical;
        }

        /*
        1. Reset the default placeholder opacity in Firefox. (https://github.com/tailwindlabs/tailwindcss/issues/3300)
        2. Set the default placeholder color to the user's configured gray 400 color.
        */

        input::placeholder,
        textarea::placeholder {
        opacity: 1;
        /* 1 */
        color: #9ca3af;
        /* 2 */
        }

        /*
        Set the default cursor for buttons.
        */

        button,
        [role="button"] {
        cursor: pointer;
        }

        /*
        Make sure disabled buttons don't get the pointer cursor.
        */

        :disabled {
        cursor: default;
        }

        /*
        1. Make replaced elements `display: block` by default. (https://github.com/mozdevs/cssremedy/issues/14)
        2. Add `vertical-align: middle` to align replaced elements more sensibly by default. (https://github.com/jensimmons/cssremedy/issues/14#issuecomment-634934210)
        This can trigger a poorly considered lint error in some tools but is included by design.
        */

        img,
        svg,
        video,
        canvas,
        audio,
        iframe,
        embed,
        object {
        display: block;
        /* 1 */
        vertical-align: middle;
        /* 2 */
        }

        /*
        Constrain images and videos to the parent width and preserve their intrinsic aspect ratio. (https://github.com/mozdevs/cssremedy/issues/14)
        */

        img,
        video {
        max-width: 100%;
        height: auto;
        }

        /* Make elements with the HTML hidden attribute stay hidden by default */

        [hidden] {
        display: none;
        }

        *, ::before, ::after{
        --tw-border-spacing-x: 0;
        --tw-border-spacing-y: 0;
        --tw-translate-x: 0;
        --tw-translate-y: 0;
        --tw-rotate: 0;
        --tw-skew-x: 0;
        --tw-skew-y: 0;
        --tw-scale-x: 1;
        --tw-scale-y: 1;
        --tw-pan-x:  ;
        --tw-pan-y:  ;
        --tw-pinch-zoom:  ;
        --tw-scroll-snap-strictness: proximity;
        --tw-gradient-from-position:  ;
        --tw-gradient-via-position:  ;
        --tw-gradient-to-position:  ;
        --tw-ordinal:  ;
        --tw-slashed-zero:  ;
        --tw-numeric-figure:  ;
        --tw-numeric-spacing:  ;
        --tw-numeric-fraction:  ;
        --tw-ring-inset:  ;
        --tw-ring-offset-width: 0px;
        --tw-ring-offset-color: #fff;
        --tw-ring-color: rgb(59 130 246 / 0.5);
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
        --tw-shadow: 0 0 #0000;
        --tw-shadow-colored: 0 0 #0000;
        --tw-blur:  ;
        --tw-brightness:  ;
        --tw-contrast:  ;
        --tw-grayscale:  ;
        --tw-hue-rotate:  ;
        --tw-invert:  ;
        --tw-saturate:  ;
        --tw-sepia:  ;
        --tw-drop-shadow:  ;
        --tw-backdrop-blur:  ;
        --tw-backdrop-brightness:  ;
        --tw-backdrop-contrast:  ;
        --tw-backdrop-grayscale:  ;
        --tw-backdrop-hue-rotate:  ;
        --tw-backdrop-invert:  ;
        --tw-backdrop-opacity:  ;
        --tw-backdrop-saturate:  ;
        --tw-backdrop-sepia:  ;
        }

        ::backdrop{
        --tw-border-spacing-x: 0;
        --tw-border-spacing-y: 0;
        --tw-translate-x: 0;
        --tw-translate-y: 0;
        --tw-rotate: 0;
        --tw-skew-x: 0;
        --tw-skew-y: 0;
        --tw-scale-x: 1;
        --tw-scale-y: 1;
        --tw-pan-x:  ;
        --tw-pan-y:  ;
        --tw-pinch-zoom:  ;
        --tw-scroll-snap-strictness: proximity;
        --tw-gradient-from-position:  ;
        --tw-gradient-via-position:  ;
        --tw-gradient-to-position:  ;
        --tw-ordinal:  ;
        --tw-slashed-zero:  ;
        --tw-numeric-figure:  ;
        --tw-numeric-spacing:  ;
        --tw-numeric-fraction:  ;
        --tw-ring-inset:  ;
        --tw-ring-offset-width: 0px;
        --tw-ring-offset-color: #fff;
        --tw-ring-color: rgb(59 130 246 / 0.5);
        --tw-ring-offset-shadow: 0 0 #0000;
        --tw-ring-shadow: 0 0 #0000;
        --tw-shadow: 0 0 #0000;
        --tw-shadow-colored: 0 0 #0000;
        --tw-blur:  ;
        --tw-brightness:  ;
        --tw-contrast:  ;
        --tw-grayscale:  ;
        --tw-hue-rotate:  ;
        --tw-invert:  ;
        --tw-saturate:  ;
        --tw-sepia:  ;
        --tw-drop-shadow:  ;
        --tw-backdrop-blur:  ;
        --tw-backdrop-brightness:  ;
        --tw-backdrop-contrast:  ;
        --tw-backdrop-grayscale:  ;
        --tw-backdrop-hue-rotate:  ;
        --tw-backdrop-invert:  ;
        --tw-backdrop-opacity:  ;
        --tw-backdrop-saturate:  ;
        --tw-backdrop-sepia:  ;
        }

        .fixed{
        position: fixed;
        }

        .bottom-0{
        bottom: 0px;
        }

        .left-0{
        left: 0px;
        }

        .table{
        display: table;
        }

        .h-12{
        height: 3rem;
        }

        .w-1\/2{
        width: 50%;
        }

        .w-full{
        width: 100%;
        }

        .border-collapse{
        border-collapse: collapse;
        }

        .border-spacing-0{
        --tw-border-spacing-x: 0px;
        --tw-border-spacing-y: 0px;
        border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
        }

        .whitespace-nowrap{
        white-space: nowrap;
        }

        .border-b{
        border-bottom-width: 1px;
        }

        .border-b-2{
        border-bottom-width: 2px;
        }

        .border-r{
        border-right-width: 1px;
        }

        .border-main{
        border-color: #5c6ac4;
        }

        .bg-main{
        background-color: #5c6ac4;
        }

        .bg-slate-100{
        background-color: #f1f5f9;
        }

        .p-3{
        padding: 0.75rem;
        }

        .px-14{
        padding-left: 3.5rem;
        padding-right: 3.5rem;
        }
        
        .px-14{
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        }

        .px-2{
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        }

        .py-10{
        padding-top: 2.5rem;
        padding-bottom: 2.5rem;
        }

        .py-3{
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        }

        .py-4{
        padding-top: 1rem;
        padding-bottom: 1rem;
        }

        .py-6{
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        }

        .pb-3{
        padding-bottom: 0.75rem;
        }

        .pl-2{
        padding-left: 0.5rem;
        }

        .pl-3{
        padding-left: 0.75rem;
        }

        .pl-4{
        padding-left: 1rem;
        }

        .pr-3{
        padding-right: 0.75rem;
        }

        .pr-4{
        padding-right: 1rem;
        }

        .text-center{
        text-align: center;
        }

        .text-right{
        text-align: right;
        }

        .align-top{
        vertical-align: top;
        }

        .text-sm{
        font-size: 0.875rem;
        line-height: 1.25rem;
        }

        .text-xs{
        font-size: 0.75rem;
        line-height: 1rem;
        }

        .font-bold{
        font-weight: 700;
        }

        .italic{
        font-style: italic;
        }

        .text-main{
        color: #5c6ac4;
        }

        .text-neutral-600{
        color: #525252;
        }

        .text-neutral-700{
        color: #404040;
        }

        .text-slate-300{
        color: #cbd5e1;
        }

        .text-slate-400{
            color: #94a3b8;
        }

        .text-white{
            color: #fff;
        }

        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        @page {
            margin-top: 40px;
            margin-bottom: 0;
            margin-right: 0;
            margin-left: 0;
        }

        @page :first{
            margin-top: 0;
            margin-bottom: 0;
            margin-right: 0;
            margin-left: 0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div>
        <div class="py-4">
            <div class="px-14 py-6">
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                        <td class="w-full align-top">
                            <div>
                                <img src="{{ public_path('img/logo.jpg') }}" class="h-12" />
                            </div>
                        </td>

                        <td class="align-top">
                            <div class="text-sm">
                                <table class="border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="border-r pr-4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400 text-right">Tanggal :</p>
                                                    <p class="whitespace-nowrap font-bold text-main text-right">{{ $formattedDate }}</p>
                                                </div>
                                            </td>
                                            <td class="pl-4">
                                                <div>
                                                    <p class="whitespace-nowrap text-slate-400 text-right">Invoice :</p>
                                                    <p class="whitespace-nowrap font-bold text-main text-right" style="text-transform: uppercase">{{ $order->invoice }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-slate-100 px-14 py-6 text-sm">
                <table class="w-full border-collapse border-spacing-0">
                    <tbody>
                        <tr>
                            <td class="w-1/2 align-top">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold" style="margin-bottom: 7px;">Penjual</p>
                                    <div class="mt-2">
                                        @foreach($transformedOrder['sellers'] as $seller)
                                            <div style="margin-top: 5px;">
                                                <p>
                                                    <strong>
                                                        @if($loop->count > 1)
                                                            Penjual {{ $loop->iteration }} :
                                                        @else
                                                            Penjual :
                                                        @endif
                                                    </strong>
                                                    {{ $seller['name'] }} ({{ $seller['phone'] }})
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="w-1/2 align-top text-right">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold" style="margin-bottom: 7px;">Penerima</p>
                                    <div class="mt-2">
                                        <p><strong>Nama :</strong> {{ $order->customer_name . ' (' . $order->customer_phone . ')' }}</p>
                                        <p><strong>Alamat :</strong> {{ $order->customer_address . ', ' }}</p>
                                        <p>{{ 'Kecamatan ' . $order->customer->district->name . ', Kota ' . $order->customer->district->city->name . ', ' }}</p>
                                        <p>{{ $order->customer->district->city->province->name . ', ' . $order->customer->district->city->postal_code }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-14 py-10 text-sm text-neutral-700">
                <table class="w-full border-collapse border-spacing-0">
                    <thead>
                        <tr>
                            <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main" colspan="2">Produk</td>
                            <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Nomor Resi</td>
                            <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Kurir</td>
                            <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Subtotal</td>
                            <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Status</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->details as $row)
                            @php
                                $weight = $row->weight;

                                if (strpos($weight, '-') !== false) {
                                    // If the weight is a range, split it into an array
                                    $weights = explode('-', $weight);
                                    $minWeight = (float) trim($weights[0]);
                                    $maxWeight = (float) trim($weights[1]);

                                    // Check if the weights are >= 1000 to display in Kg
                                    $minWeightDisplay = $minWeight >= 1000 ? ($minWeight / 1000) : $minWeight;
                                    $maxWeightDisplay = $maxWeight >= 1000 ? ($maxWeight / 1000) . ' Kg' : $maxWeight . ' gram / pack';

                                    // Construct the display string
                                    $weightDisplay = $minWeightDisplay . ' - ' . $maxWeightDisplay;
                                } else {
                                    // Single weight value
                                    $weightDisplay = $weight >= 1000 ? ($weight / 1000) . ' Kg' : $weight . ' gram / pack';
                                }

                                // Find the return specific to this product and order (if exists)
                                $productReturn = $order->return->first(function ($return) use ($row) {
                                    return $return->order_id === $row->order_id && $return->product_id === $row->product_id;
                                });
                            @endphp
                            <tr>
                                <td class="border-b py-3 pl-2">
                                    <div style="border: 1px solid transparent; border-radius: 4px; display: block; width: 80px; height: 80px;">
                                        <img src="{{ public_path('/products/' . $row->product->image) }}" alt="{{ $row->product->name }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: 4px;">
                                    </div>
                                </td>
                                <td class="border-b py-3 pl-2">
                                    <p class="font-bold">{{ $row->product->name }}</p>
                                    <p style="margin-top: 5px;">{{ $row->qty . ' x Rp ' . number_format($row->price, 0, ',', '.') }}</p>
                                    <p style="margin-top: 5px;">{{ $weightDisplay }}</p>
                                </td>
                                @if($row->tracking_number !== null)
                                    <td class="border-b py-3 pl-2 font-bold">{{ '#' . $row->tracking_number }}</td>
                                @else
                                    <td class="border-b py-3 pl-2 font-bold">-</td>
                                @endif
                                <td class="border-b py-3 pl-2">{{ $row->shipping_service }}</td>
                                <td class="border-b py-3 pl-2">{{ 'Rp ' . number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                <td class="border-b py-3 pl-2">
                                    @if($productReturn)
                                        (Return) {!! $productReturn->status_label !!}
                                    @else
                                        {!! $row->status_label !!}
                                    @endif
                                </td>
                            </tr>
                            {{-- <tr>
                                <td class="border-b py-3 pl-3">{{ $loop->iteration }}</td>
                                <td class="border-b py-3 pl-2">{{ $row->product->name }}</td>
                                <td class="border-b py-3 pl-2">Rp {{ number_format($row->price, 0, ',', '.') }}</td>
                                <td class="border-b py-3 pl-2">{{ $row->qty }} Item</td>
                                <td class="border-b py-3 pl-2">Rp {{ number_format($row->price * $row->qty, 0, ',', '.') }}</td>
                                <td class="border-b py-3 pl-2">Rp {{ number_format($row->shipping_cost, 0, ',', '.') }}</td>
                                <td class="border-b py-3 pl-2"><span style="text-transform: uppercase;">{{ $row->shipping_courier }}</span> (Layanan : {{ $row->shipping_service }})</td>
                                <td class="border-b py-3 pl-2">{{ 'Rp ' . number_format($row->price * $row->qty + $row->price * $row->qty * 0.10 + $row->shipping_cost, 0, ',', '.') }}</td>
                            </tr> --}}
                        @endforeach
                        <tr>
                            <td colspan="8">
                                <table class="w-full border-collapse border-spacing-0">
                                    <tbody>
                                        <tr>
                                            <td class="w-full"></td>
                                            <td>
                                                <table class="w-full border-collapse border-spacing-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Subtotal : </div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">{{ 'Rp ' . number_format($order->subtotal, 0, ',', '.') }}</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Ongkos Kirim :</div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">{{ 'Rp ' . number_format($order->cost, 0, ',', '.') }}</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Biaya Layanan :</div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">{{ 'Rp ' . number_format($order->service_cost, 0, ',', '.') }}</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="border-b p-3">
                                                                <div class="whitespace-nowrap text-slate-400">Biaya Kemasan :</div>
                                                            </td>
                                                            <td class="border-b p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-main">{{ 'Rp ' . number_format($order->packaging_cost, 0, ',', '.') }}</div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="bg-main p-3">
                                                                <div class="whitespace-nowrap font-bold text-white">Total :</div>
                                                            </td>
                                                            <td class="bg-main p-3 text-right">
                                                                <div class="whitespace-nowrap font-bold text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-14 py-10 text-sm text-neutral-700">
                @if ($order->payment)
                    <p class="text-main font-bold" style="margin-bottom: 7px;">Detail Pembayaran</p>
                    <p><span class="text-main">Pengirim : </span> {{ $order->payment->name }}</p>
                    <p><span class="text-main">Tanggal : </span> {{ \Carbon\Carbon::parse($order->payment->transfer_date)->locale('id')->translatedFormat('l, d M Y H:i:s') . ' WIB' }}</p>
                    <p><span class="text-main">Metode Pembayaran : </span> {!! $order->payment->payment_method_name . ' - ' . $order->payment->acquirer_name !!}</p>
                    <p><span class="text-main">Total Pembayaran :  {{ 'Rp ' . number_format($order->payment->amount, 0, ',', '.') }}</span></p>
                @else
                    <p class="text-main font-bold" style="margin-bottom: 10px;">Detail Pembayaran</p>
                    <p>Pengirim : -</p>
                    <p>Metode Pembayaran : -</p>
                    <p>Tanggal : -</p>    
                    <p>Pembayaran : Rp 0</p>    
                @endif
            </div>

            {{-- <div class="px-14 py-10 text-sm text-neutral-700">
                <p class="text-main font-bold">Notes</p>
                <p class="italic">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries
                for previewing layouts and visual mockups.</p>
                </dvi>

                <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
                Supplier Company
                <span class="text-slate-300 px-2">|</span>
                info@company.com
                <span class="text-slate-300 px-2">|</span>
                +1-202-555-0106
                </footer>
            </div> --}}
        </div>
    </body>

</html>