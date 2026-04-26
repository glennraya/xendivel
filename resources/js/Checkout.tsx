import axios from 'axios';
import { useEffect, useState } from 'react';

declare const Xendit: any;

type XenditErrorItem = {
    message: string;
    path: string;
};

type XenditErrorsState = {
    error_code: string;
    message: string;
    errors: XenditErrorItem[];
};

type AuthorizedCharge = {
    id: string;
    status: string;
    external_id: string;
    authorized_amount: number | string;
};

type CardDetails = {
    card_number: string;
    card_exp_month: string;
    card_exp_year: string;
    card_cvn: string;
};

type AuthenticationResponse = {
    id: string;
    payer_authentication_url: string;
    status: string;
    credit_card_token_id: string;
    failure_reason: string;
};

const EMPTY_ERRORS: XenditErrorsState = {
    error_code: '',
    message: '',
    errors: [],
};

const Checkout = () => {
    const [isXenditLoaded, setXenditLoaded] = useState(false);
    const [paymentMethod, setPaymentMethod] = useState<'card' | 'ewallet'>('card');
    const [cardMode, setCardMode] = useState<'charge' | 'authorize'>('charge');
    const [apiResponse, setApiResponse] = useState('');
    const [cardError, setCardError] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const [errors, setErrors] = useState<XenditErrorsState>(EMPTY_ERRORS);
    const [otpUrl, setOtpUrl] = useState('');
    const [authenticating, setAuthenticating] = useState(false);
    const [isMultiUse, setIsMultiUse] = useState(false);
    const [isActionRunning, setIsActionRunning] = useState(false);
    const [showAuthorizedChargeActions, setShowAuthorizedChargeActions] = useState(false);
    const [authorizedCharge, setAuthorizedCharge] = useState<AuthorizedCharge | null>(null);
    const [captureAmount, setCaptureAmount] = useState('');
    const [reversalExternalId, setReversalExternalId] = useState('');
    const [cardDetails, setCardDetails] = useState<CardDetails>({
        card_number: '',
        card_exp_month: '',
        card_exp_year: '',
        card_cvn: '',
    });
    const [currency, setCurrency] = useState('PHP');
    const [amount, setAmount] = useState('');

    const handleFormChange = (event: React.ChangeEvent<HTMLInputElement>, obj: string) => {
        const key = event.target.id as keyof CardDetails;
        const value = event.target.value;

        if (obj === 'cardDetails') {
            setCardDetails((values) => ({
                ...values,
                [key]: value,
            }));
        }
    };

    const formatCardNumber = (cardNumber: string) => {
        return cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
    };

    const resetFeedback = () => {
        setCardError('');
        setErrorMessage('');
        setErrors(EMPTY_ERRORS);
    };

    const showResponse = (response: Record<string, any>) => {
        setApiResponse(JSON.stringify(response, null, 2));
    };

    const normalizeAuthorizedCharge = (response: Record<string, any>) => {
        const authorizedAmount = response.authorized_amount ?? response.capture_amount ?? amount;

        setAuthorizedCharge({
            id: response.id,
            status: response.status,
            external_id: response.external_id ?? '',
            authorized_amount: authorizedAmount,
        });

        if (authorizedAmount !== undefined && authorizedAmount !== null) {
            setCaptureAmount(String(authorizedAmount));
        }

        if (response.external_id) {
            setReversalExternalId(response.external_id);
        }
    };

    const parseAxiosError = (error: any) => {
        const fallback = {
            message: error?.response?.data?.message || error?.message || 'Unable to process the payment request.',
            errors: [] as XenditErrorItem[],
        };

        const rawMessage = error?.response?.data?.message;

        if (typeof rawMessage === 'string') {
            try {
                const parsed = JSON.parse(rawMessage);

                return {
                    message: parsed.message || fallback.message,
                    errors: Array.isArray(parsed.errors) ? parsed.errors : fallback.errors,
                };
            } catch {
                return fallback;
            }
        }

        return fallback;
    };

    const handleApiError = (error: any) => {
        const parsedError = parseAxiosError(error);
        setErrorMessage(parsedError.message);
        setErrors({
            ...EMPTY_ERRORS,
            errors: parsedError.errors,
        });
    };

    const submitCardPayment = async (response: AuthenticationResponse) => {
        const endpoint = cardMode === 'authorize' ? '/authorize-card' : '/pay-with-card';

        const payload = {
            token_id: response.credit_card_token_id,
            authentication_id: response.id,
            currency,
            amount,
        };

        try {
            const { data } = await axios.post(endpoint, payload);

            showResponse(data);
            setOtpUrl('');
            setAuthenticating(false);

            if (data.status === 'AUTHORIZED') {
                normalizeAuthorizedCharge(data);
                setShowAuthorizedChargeActions(true);
            } else if (!authorizedCharge || authorizedCharge.id !== data.id) {
                setAuthorizedCharge(null);
                setShowAuthorizedChargeActions(false);
            }
        } catch (error) {
            setAuthenticating(false);
            handleApiError(error);
        }
    };

    const payWithCard = async (event: React.SyntheticEvent) => {
        event.preventDefault();

        resetFeedback();
        setAuthenticating(true);

        const cardNumber = Xendit.card.validateCardNumber(cardDetails.card_number);

        const expiryDate = Xendit.card.validateExpiry(cardDetails.card_exp_month, cardDetails.card_exp_year);

        const cvn = Xendit.card.validateCvn(cardDetails.card_cvn);

        if (cardNumber === false || expiryDate === false || cvn === false) {
            setAuthenticating(false);
            setCardError('Invalid card details. Please check your card details and try again.');
            return;
        }

        await Xendit.card.createToken(
            {
                amount,
                card_number: cardDetails.card_number,
                card_exp_month: cardDetails.card_exp_month,
                card_exp_year: cardDetails.card_exp_year,
                card_cvn: cardDetails.card_cvn,
                is_multiple_use: isMultiUse,
                should_authenticate: true,
            },
            tokenizationHandler,
        );
    };

    const tokenizationHandler = (err: { message: string; errors?: XenditErrorItem[] } | null, cardToken: { id: string }) => {
        if (err) {
            setAuthenticating(false);
            setErrorMessage(err.message);
            setErrors({
                ...EMPTY_ERRORS,
                errors: err.errors ?? [],
            });
            return;
        }

        Xendit.card.createAuthentication(
            {
                amount,
                token_id: cardToken.id,
            },
            authenticationHandler,
        );
    };

    const authenticationHandler = async (err: { message: string } | null, response: AuthenticationResponse) => {
        if (err) {
            setAuthenticating(false);
            setErrorMessage(err.message);
            return;
        }

        switch (response.status) {
            case 'VERIFIED':
                await submitCardPayment(response);
                break;

            case 'IN_REVIEW':
                setOtpUrl(response.payer_authentication_url);
                break;

            case 'FAILED':
                setOtpUrl('');
                setAuthenticating(false);
                setErrorMessage(
                    response.failure_reason === 'AUTHENTICATION_FAILED'
                        ? 'Authentication failed. Please make sure you entered your OTP code correctly and try again.'
                        : 'We encountered an error that prevents the payment from being fulfilled. Please check your card details and make sure you entered the OTP correctly.',
                );
                break;

            default:
                setAuthenticating(false);
                break;
        }
    };

    const captureAuthorizedCharge = async () => {
        if (!authorizedCharge?.id) {
            return;
        }

        resetFeedback();
        setIsActionRunning(true);

        try {
            const { data } = await axios.post('/capture-card-charge', {
                charge_id: authorizedCharge.id,
                amount: parseInt(captureAmount, 10),
            });

            showResponse(data);
            normalizeAuthorizedCharge(data);
            setShowAuthorizedChargeActions(false);
        } catch (error) {
            handleApiError(error);
        } finally {
            setIsActionRunning(false);
        }
    };

    const voidAuthorizedCharge = async () => {
        if (!authorizedCharge?.id) {
            return;
        }

        resetFeedback();
        setIsActionRunning(true);

        try {
            const payload: { charge_id: string; external_id?: string } = {
                charge_id: authorizedCharge.id,
            };

            if (reversalExternalId.trim() !== '') {
                payload.external_id = reversalExternalId;
            }

            const { data } = await axios.post('/void-card-authorization', payload);

            showResponse(data);
            normalizeAuthorizedCharge(data);
        } catch (error) {
            handleApiError(error);
        } finally {
            setIsActionRunning(false);
        }
    };

    const payWithEwallet = async (event: React.MouseEvent<HTMLButtonElement>) => {
        event.preventDefault();
        resetFeedback();

        await axios
            .post('/pay-via-ewallet', {
                amount: parseInt(amount, 10),
                currency: 'PHP',
                checkout_method: 'ONE_TIME_PAYMENT',
                channel_code: 'PH_GCASH',
                channel_properties: {
                    success_redirect_url: `${window.location.origin}/xendivel/payment/success`,
                    failure_redirect_url: `${window.location.origin}/xendivel/payment/failed`,
                },
            })
            .then((response) => {
                window.location.href = response.data.actions.desktop_web_checkout_url;
            })
            .catch(handleApiError);
    };

    const loadScript = (src: string, id: string): Promise<void> => {
        return new Promise<void>((resolve, reject) => {
            if (document.getElementById(id)) {
                resolve();
                return;
            }
            const script = document.createElement('script');
            script.src = src;
            script.id = id;
            script.onload = () => resolve();
            script.onerror = () => reject(new Error(`Failed to load ${src}`));
            document.body.appendChild(script);
        });
    };

    useEffect(() => {
        loadScript('https://js.xendit.co/v1/xendit.min.js', 'xendit-script')
            .then(() => {
                setXenditLoaded(true);
                Xendit.setPublishableKey('');
            })
            .catch((error) => console.error('Failed to load Xendit script:', error));
    }, [isXenditLoaded]);

    const hasResponse = apiResponse !== '' || errorMessage !== '' || errors.errors.length > 0;

    return (
        <>
            {authenticating ? (
                <div className="bg-opacity-75 fixed top-0 left-0 z-10 flex h-full w-full items-center justify-center bg-black backdrop-blur-md">
                    <div className="flex h-3/4 max-w-2xl flex-col items-center justify-center overflow-hidden rounded-xl bg-white p-8 shadow-2xl">
                        <span className="w-3/4 text-center text-xl font-bold">
                            Please confirm your identity by entering the one-time password (OTP) provided to you.
                        </span>
                        <iframe src={otpUrl} className="h-full w-full"></iframe>
                    </div>
                </div>
            ) : null}

            <div className="container mx-auto flex flex-col items-center justify-center gap-4">
                <header className="mt-8 text-sm">
                    <h1 className="mb-2 text-xl font-bold">Xendivel Checkout Example</h1>
                    <p className="flex gap-3">
                        <a
                            href="https://docs.xendit.co/credit-cards/integrations/test-scenarios"
                            className="border-b border-blue-600 text-blue-600"
                            target="_tab"
                        >
                            Test card numbers
                        </a>

                        <a
                            href="https://docs.xendit.co/docs/cards-simulate-card-scenarios#simulate-a-failed-charge-payment"
                            className="border-b border-blue-600 text-blue-600"
                            target="_tab"
                        >
                            Test failed scenarios
                        </a>
                    </p>
                </header>

                <div className="mt-8 flex w-[500px] flex-col rounded-md border border-gray-300">
                    <div className="flex w-full text-sm">
                        <span
                            className={`flex-1 cursor-pointer p-4 text-center ${
                                paymentMethod === 'card' ? 'bg-white font-bold text-black' : 'bg-gray-200 hover:bg-gray-100'
                            } rounded-tl-md`}
                            onClick={() => setPaymentMethod('card')}
                        >
                            Credit/Debit Card
                        </span>
                        <span
                            className={`flex-1 cursor-pointer p-4 text-center ${
                                paymentMethod === 'ewallet' ? 'bg-white font-bold text-black' : 'bg-gray-200 hover:bg-gray-100'
                            } rounded-tr-md`}
                            onClick={() => setPaymentMethod('ewallet')}
                        >
                            E-Wallet
                        </span>
                    </div>

                    <div
                        className={`flex flex-col rounded-br-md rounded-bl-md bg-white p-8 shadow-md ${
                            paymentMethod === 'card' ? 'flex' : 'hidden'
                        } font-medium`}
                    >
                        <input
                            placeholder="Amount to pay"
                            type="text"
                            className="mb-2 rounded-md border border-gray-300 p-2"
                            value={amount}
                            onChange={(e) => setAmount(e.target.value)}
                        />
                        <select
                            className="mb-4 rounded-md border border-gray-300 p-2"
                            value={cardMode}
                            onChange={(e) => setCardMode(e.target.value as 'charge' | 'authorize')}
                        >
                            <option value="charge">Charge now</option>
                            <option value="authorize">Authorize hold</option>
                        </select>
                        <form
                            onSubmit={payWithCard}
                            className="mb-4 flex flex-col overflow-hidden rounded-md border border-gray-300 bg-gray-100 shadow-sm"
                        >
                            <div className="flex border-b border-gray-300">
                                <div className="flex w-full flex-col">
                                    <div className="flex flex-col">
                                        <div className="relative flex">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                data-slot="icon"
                                                className="absolute top-1/2 right-0 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                            >
                                                <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                                                <path
                                                    fillRule="evenodd"
                                                    d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                            <input
                                                type="text"
                                                id="card_number"
                                                name="card-number"
                                                className="w-full border-none bg-gray-100 p-3 ring-0 outline-none focus:bg-gray-200 focus:ring-0"
                                                placeholder="Card number"
                                                value={formatCardNumber(cardDetails.card_number)}
                                                onChange={(e) => handleFormChange(e, 'cardDetails')}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="flex flex-col">
                                <div className="flex">
                                    <div className="flex w-1/2">
                                        <input
                                            type="text"
                                            id="card_exp_month"
                                            name="card-exp-month"
                                            className="w-14 border-none bg-gray-100 p-3 ring-0 outline-none focus:bg-gray-200 focus:ring-0"
                                            placeholder="MM"
                                            maxLength={2}
                                            value={cardDetails.card_exp_month}
                                            onChange={(e) => handleFormChange(e, 'cardDetails')}
                                        />
                                        <span className="self-center px-3 font-bold text-gray-500">/</span>
                                        <input
                                            type="text"
                                            id="card_exp_year"
                                            name="card-exp-year"
                                            className="w-auto border-none bg-gray-100 p-3 ring-0 outline-none focus:bg-gray-200 focus:ring-0"
                                            placeholder="YYYY"
                                            maxLength={4}
                                            value={cardDetails.card_exp_year}
                                            onChange={(e) => handleFormChange(e, 'cardDetails')}
                                        />
                                    </div>
                                    <div className="relative flex w-1/2 border-l border-gray-300">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            data-slot="icon"
                                            className="absolute top-1/2 right-0 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                        <input
                                            type="text"
                                            id="card_cvn"
                                            name="card-cvn"
                                            className="w-full border-none bg-gray-100 p-3 ring-0 outline-none focus:bg-gray-200 focus:ring-0"
                                            placeholder="CVV"
                                            maxLength={4}
                                            value={cardDetails.card_cvn}
                                            onChange={(e) => handleFormChange(e, 'cardDetails')}
                                        />
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div
                            className={`col-span-6 mb-4 justify-center gap-x-4 rounded-md bg-red-200 p-3 font-medium text-red-800 ${
                                cardError !== '' ? 'flex' : 'hidden'
                            }`}
                        >
                            {cardError}
                        </div>
                        <div className="mb-4 rounded-md border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            <span className="block font-semibold">Card payment mode</span>
                            <span className="mt-1 block">
                                {cardMode === 'authorize'
                                    ? 'Authorize hold stores the card hold first so you can capture the full or partial amount later.'
                                    : 'Charge now captures the payment immediately after successful 3DS authentication.'}
                            </span>
                        </div>
                        <div className="col-span-6 flex items-center gap-x-4 rounded-md border border-gray-300 p-4 text-sm font-medium">
                            <label htmlFor="save-card-checkbox" className="order-2">
                                Save my information for faster checkout
                            </label>
                            <input id="save-card-checkbox" type="checkbox" checked={isMultiUse} onChange={() => setIsMultiUse(!isMultiUse)} />
                        </div>
                        <div className="mt-4 flex flex-col gap-4">
                            <button
                                type="button"
                                className="w-full rounded-md bg-black py-3 text-sm font-bold text-white uppercase hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
                                disabled={authenticating}
                                onClick={payWithCard}
                            >
                                {cardMode === 'authorize' ? 'Authorize Hold' : 'Charge Card'}
                            </button>
                        </div>

                        {authorizedCharge ? (
                            <div className="mt-6 flex flex-col gap-4 rounded-md border border-gray-300 bg-gray-50 p-4 text-sm">
                                <div className="flex flex-col gap-1">
                                    <span className="font-bold">Authorized Charge Actions</span>
                                    <span>Charge ID: {authorizedCharge.id}</span>
                                    <span>Status: {authorizedCharge.status}</span>
                                    <span>Authorized Amount: {authorizedCharge.authorized_amount}</span>
                                    <span>External ID: {authorizedCharge.external_id || 'N/A'}</span>
                                </div>

                                <input
                                    type="text"
                                    className="rounded-md border border-gray-300 p-2"
                                    value={captureAmount}
                                    onChange={(e) => setCaptureAmount(e.target.value)}
                                    placeholder="Capture amount"
                                />

                                <input
                                    type="text"
                                    className="rounded-md border border-gray-300 p-2"
                                    value={reversalExternalId}
                                    onChange={(e) => setReversalExternalId(e.target.value)}
                                    placeholder="Reversal external ID (optional when auto ID is enabled)"
                                />

                                {showAuthorizedChargeActions ? (
                                    <div className="grid grid-cols-2 gap-4">
                                        <button
                                            type="button"
                                            className="rounded-md bg-black py-3 text-sm font-bold text-white uppercase hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
                                            disabled={isActionRunning}
                                            onClick={captureAuthorizedCharge}
                                        >
                                            Capture Authorized Charge
                                        </button>
                                        <button
                                            type="button"
                                            className="rounded-md bg-red-600 py-3 text-sm font-bold text-white uppercase hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50"
                                            disabled={isActionRunning}
                                            onClick={voidAuthorizedCharge}
                                        >
                                            Void Authorization
                                        </button>
                                    </div>
                                ) : null}
                            </div>
                        ) : null}
                    </div>

                    <div
                        className={`grid w-full grid-cols-6 gap-4 rounded-br-md rounded-bl-md bg-white p-8 shadow-sm ${
                            paymentMethod === 'ewallet' ? 'flex' : 'hidden'
                        }`}
                    >
                        <input
                            placeholder="Amount to pay"
                            type="text"
                            className="col-span-6 mb-2 rounded-md border border-gray-300 p-2"
                            value={amount}
                            onChange={(e) => setAmount(e.target.value)}
                        />
                        <button
                            className="col-span-6 rounded-md bg-black py-3 text-sm font-bold text-white uppercase hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black"
                            onClick={payWithEwallet}
                        >
                            Charge with eWallet
                        </button>
                    </div>
                </div>

                {hasResponse ? (
                    <div className="my-2 flex w-[500px] flex-col gap-4 rounded-md border border-gray-300 bg-white p-8 whitespace-nowrap shadow-md">
                        <span className="mb-2 text-lg font-bold">Xendit API Response</span>

                        {apiResponse !== '' ? (
                            <>
                                {isMultiUse ? (
                                    <span className="mb-2 flex items-center gap-4 text-sm whitespace-pre-wrap">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            data-slot="icon"
                                            className="h-8 w-8 text-blue-600"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                                clipRule="evenodd"
                                            />
                                        </svg>

                                        <span className="flex-1">
                                            If you choose to save this card for future transactions, make sure to store the{' '}
                                            <code className="rounded bg-gray-200 px-2 py-1 text-xs">credit_card_token_id</code> in your database. This
                                            token is necessary for future charges without re-entering card details.
                                        </span>
                                    </span>
                                ) : null}
                                <pre className="rounded-md bg-gray-100 p-4 text-xs leading-relaxed whitespace-pre-wrap">{apiResponse}</pre>
                            </>
                        ) : null}

                        {errorMessage !== '' ? (
                            <pre className="rounded-md bg-gray-100 p-4 text-xs leading-relaxed whitespace-pre-wrap">{errorMessage}</pre>
                        ) : null}

                        {errors.errors.length > 0 ? (
                            <pre className="rounded-md bg-gray-100 p-4 text-xs leading-relaxed whitespace-pre-wrap">
                                {errors.errors.map((error, index) => (
                                    <span key={index} className="flex w-full justify-between">
                                        <span>{error.path}</span>
                                        <span>{error.message}</span>
                                    </span>
                                ))}
                            </pre>
                        ) : null}
                    </div>
                ) : null}
            </div>
        </>
    );
};

export default Checkout;
