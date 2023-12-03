{{-- Payment Response --}}
<div id="charge-response" class="hidden flex-col bg-white p-4 rounded-xl shadow-md gap-y-2 w-full xl:w-1/2">
    <span class="font-bold">API Response:</span>
    <span>When the <code class="font-bold">status</code> is <code class="font-bold">CAPTURED</code> it means that the payment is <span class="text-green-500 font-bold">successful</span>. You can now do something using this data, typically saving some or all data to the database, displaying a message to the customer about the payment, or generate invoice using Xendivel's own Invoice API.</span>
    <pre class="bg-gray-100 p-4 rounded-xl mt-2 whitespace-pre-wrap text-sm"></pre>
</div>

{{-- Error Panel --}}
<div id="errorDiv" class="hidden flex-col bg-white p-4 rounded-xl shadow-md gap-y-2 w-full xl:w-1/2">
    <span class="font-bold">Error:</span>
    <pre id="error-code" class="bg-gray-100 p-4 text-center rounded-xl whitespace-pre-wrap"></pre>
    <pre id="error-message" class="bg-gray-100 p-4 text-center rounded-xl mt-2 whitespace-pre-wrap"></pre>
    <span>Using this error code, you can give the user a customized message based on the error code. <span class="font-bold">You could also check your console for more information.</span></span>
    <span class="font-medium mt-4">Xendit Documentation:</span>
    <ul class="flex flex-col gap-2">
        <li><a href="https://docs.xendit.co/credit-cards/understanding-card-declines#sidebar" class="text-blue-500 border-b border-blue-500" target="_tab">Understanding card declines</a></li>
        <li><a href="https://developers.xendit.co/api-reference/#capture-charge" class="text-blue-500 border-b border-blue-500" target="_tab">Capture card — error codes</a></li>
        <li><a href="https://developers.xendit.co/api-reference/#create-token" class="text-blue-500 border-b border-blue-500" target="_tab">Create token — error codes</a></li>
    </ul>
</div>
