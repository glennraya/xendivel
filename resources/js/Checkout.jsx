import { useState, useEffect } from 'react'
import axios from 'axios'

const Checkout = () => {
    const [isXenditLoaded, setXenditLoaded] = useState(false)

    // Payment method states (card, ewallet).
    const [paymentMethod, setPaymentMethod] = useState('card')

    // API response.
    const [apiResponse, setApiResponse] = useState('')

    // Error states.
    const [cardError, setCardError] = useState('')
    const [errorMessage, setErrorMessage] = useState('')
    const [errors, setErrors] = useState({
        error_code: '',
        message: '',
        errors: [
            {
                message: '',
                path: '',
            },
        ],
    })

    // OTP URL (Will show the OTP dialog when value is set.)
    const [otpUrl, setOtpUrl] = useState('')

    // Determines whether to show the OTP dialog or not.
    const [authenticating, setAuthenticating] = useState(false)

    // Determines if the card token is for single or multi-use.
    const [isMultiUse, setIsMultiUse] = useState(false)

    // Credit/debit card details.
    const [cardDetails, setCardDetails] = useState({
        card_number: '',
        card_exp_month: '',
        card_exp_year: '',
        card_cvn: '',
    })

    // Example currency for the charge.
    const [currency, setCurrency] = useState('PHP')

    // Example amount to be charged.
    const [amount, setAmount] = useState('')

    // Update state when changing form values.
    const handleFormChange = (event, obj) => {
        const key = event.target.id
        const value = event.target.value

        switch (obj) {
            case 'cardDetails':
                setCardDetails(values => ({
                    ...values,
                    [key]: value,
                }))
                break

            default:
                break
        }
    }

    // Format card number
    const formatCardNumber = cardNumber => {
        return cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ')
    }

    // Pay with card
    const payWithCard = async event => {
        event.preventDefault()

        // Remove the error banner message.
        setErrorMessage('')

        // Disable the checkout button by setting 'authenticating' to true.
        setAuthenticating(true)

        // Validate the card details.
        const card_number = Xendit.card.validateCardNumber(
            cardDetails.card_number,
        )

        const expiry_date = Xendit.card.validateExpiry(
            cardDetails.card_exp_month,
            cardDetails.card_exp_year,
        )

        const cvn = Xendit.card.validateCvn(cardDetails.card_cvn)

        if (card_number === false || expiry_date === false || cvn === false) {
            setAuthenticating(false)
            setCardError(
                'Invalid card details. Please check your card details and try again.',
            )
            return
        }

        setCardError('')

        // Tokenize the card details.
        await Xendit.card.createToken(
            {
                amount: amount,
                card_number: cardDetails.card_number,
                card_exp_month: cardDetails.card_exp_month,
                card_exp_year: cardDetails.card_exp_year,
                card_cvn: cardDetails.card_cvn,
                is_multiple_use: isMultiUse,
                should_authenticate: true,
            },
            tokenizationHandler,
        )
    }

    // Tokenization callback handler.
    const tokenizationHandler = (err, cardToken) => {
        if (err) {
            console.log('Tokenization Error: ', err)
            setAuthenticating(false)
            setErrorMessage(err.message)
            setErrors(err)
            return
        }

        console.log('Card token:' + cardToken.id)

        const card_token = cardToken.id

        // Perform authentication of the card token.
        Xendit.card.createAuthentication(
            {
                amount: amount,
                // amount: '10055',
                token_id: card_token,
            },
            authenticationHandler,
        )
    }

    // Authentication callback handler.
    const authenticationHandler = (err, response) => {
        switch (response.status) {
            case 'VERIFIED':
                console.log('Verified!!!', response)
                console.log(response.credit_card_token_id)

                axios
                    .post('/pay-with-card', {
                        token_id: response.credit_card_token_id,
                        authentication_id: response.id,
                        currency: currency,
                        amount: amount,
                        // amount: '10055',
                    })
                    .then(response => {
                        setAuthenticating(false)
                        console.log('Response:', response)

                        if (response.data.status === 'CAPTURED') {
                            console.log('Success: ', response.data)
                            setApiResponse(
                                JSON.stringify(response.data, null, 2),
                            )
                        }
                        if (response.data.status === 'FAILED') {
                            console.log('Failed: ', response.data)
                            setApiResponse(
                                JSON.stringify(response.data, null, 2),
                            )
                        }

                        // Close the OTP dialog.
                        setOtpUrl('')
                    })
                    .catch(error => {
                        console.log('Error: ', error)
                        // setErrors(error)
                    })
                break

            case 'IN_REVIEW':
                console.log('In Review!!!', response)
                setOtpUrl(response.payer_authentication_url)
                break

            case 'FAILED':
                console.log('Failed!!!', response)
                setOtpUrl('')
                setAuthenticating(false)

                if (response.failure_reason === 'AUTHENTICATION_FAILED') {
                    setErrorMessage(
                        'Authentication Failed. Please make sure you entered your OTP code correctly and try again.',
                    )
                } else {
                    setErrorMessage(
                        'We encountered an error that prevents the payment to be fulfilled. Please check your card details and make sure you entered the OTP correctly.',
                    )
                }
                break

            default:
                break
        }
    }

    // Pay with e-Wallet
    const payWithEwallet = async event => {
        event.preventDefault()
        await axios
            .post('/pay-via-ewallet', {
                // You can test different failure scenarios by using the 'magic amount' from Xendit.
                amount: parseInt(amount),
                currency: 'PHP',
                checkout_method: 'ONE_TIME_PAYMENT',
                channel_code: 'PH_GCASH',
                channel_properties: {
                    success_redirect_url:
                        'https://package.test/ewallet/success',
                    failure_redirect_url: 'https://package.test/ewallet/failed',
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

                setErrorMessage(err.message)
                setErrors(err)
            })
    }

    // Load Xendit.js library for credit/debit card tokenization process.
    const loadScript = (src, id) => {
        return new Promise((resolve, reject) => {
            if (document.getElementById(id)) {
                resolve()
                return
            }
            const script = document.createElement('script')
            script.src = src
            script.id = id
            script.onload = () => resolve()
            script.onerror = () => reject(new Error(`Failed to load ${src}`))
            document.body.appendChild(script)
        })
    }

    useEffect(() => {
        // Load the Xendit.js library.
        loadScript('https://js.xendit.co/v1/xendit.min.js', 'xendit-script')
            .then(() => {
                setXenditLoaded(true)

                // Set your 'public' key here.
                Xendit.setPublishableKey('')
            })
            .catch(error =>
                console.error('Failed to load Xendit script:', error),
            )
    }, [isXenditLoaded])
    return (
        <>
            {/* OTP Dialog */}
            {authenticating ? (
                <div className="fixed left-0 top-0 z-10 flex h-full w-full items-center justify-center bg-black bg-opacity-75 backdrop-blur-md">
                    <div className="flex h-3/4 max-w-2xl flex-col items-center justify-center overflow-hidden rounded-xl bg-white p-8 shadow-2xl">
                        <span className="w-3/4 text-center text-xl font-bold">
                            Please confirm your identity by entering the
                            one-time password (OTP) provided to you.
                        </span>
                        <iframe src={otpUrl} className="h-full w-full"></iframe>
                    </div>
                </div>
            ) : null}

            <div className="container mx-auto flex flex-col items-center justify-center gap-4">
                <header className="mt-8 text-sm">
                    <h1 className="mb-2 text-xl font-bold">
                        Xendivel Checkout Example
                    </h1>
                    <p className="flex gap-3">
                        <a
                            href="https://docs.xendit.co/credit-cards/integrations/test-scenarios"
                            className="border-b border-blue-600 text-blue-600"
                            target="_tab"
                        >
                            Test card numbers
                        </a>

                        <a
                            href="https://docs.xendit.co/credit-cards/integrations/test-scenarios#simulating-failed-charge-transactions"
                            className="border-b border-blue-600 text-blue-600"
                            target="_tab"
                        >
                            Test failed scenarios
                        </a>
                    </p>
                </header>

                {/* Payment form */}
                <div className="mt-8 flex w-[500px] flex-col rounded-md border border-gray-300">
                    <div className="flex w-full text-sm">
                        <span
                            className={`flex-1 cursor-pointer p-4 text-center ${
                                paymentMethod === 'card'
                                    ? 'bg-white font-bold text-black'
                                    : 'bg-gray-200 hover:bg-gray-100'
                            } rounded-tl-md`}
                            onClick={() => setPaymentMethod('card')}
                        >
                            Credit/Debit Card
                        </span>
                        <span
                            className={`flex-1 cursor-pointer p-4 text-center ${
                                paymentMethod === 'ewallet'
                                    ? 'bg-white font-bold text-black'
                                    : 'bg-gray-200 hover:bg-gray-100'
                            } rounded-tr-md`}
                            onClick={() => setPaymentMethod('ewallet')}
                        >
                            E-Wallet
                        </span>
                    </div>

                    {/* Card payment */}
                    <div
                        className={`flex flex-col rounded-bl-md rounded-br-md bg-white p-8 shadow-md ${
                            paymentMethod === 'card' ? 'flex' : 'hidden'
                        } font-medium`}
                    >
                        <input
                            placeholder="Amount to pay"
                            type="text"
                            className="mb-2 rounded-md border border-gray-300"
                            value={amount}
                            onChange={e => setAmount(e.target.value)}
                        ></input>
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
                                                className="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
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
                                                className="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                                placeholder="Card number"
                                                value={formatCardNumber(
                                                    cardDetails.card_number,
                                                )}
                                                onChange={e =>
                                                    handleFormChange(
                                                        e,
                                                        'cardDetails',
                                                    )
                                                }
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
                                            className="w-14 border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                            placeholder="MM"
                                            maxLength={2}
                                            value={cardDetails.card_exp_month}
                                            onChange={e =>
                                                handleFormChange(
                                                    e,
                                                    'cardDetails',
                                                )
                                            }
                                        />
                                        <span className="self-center px-3 font-bold text-gray-500">
                                            /
                                        </span>
                                        <input
                                            type="text"
                                            id="card_exp_year"
                                            name="card-exp-year"
                                            className="w-auto border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                            placeholder="YYYY"
                                            maxLength={4}
                                            value={cardDetails.card_exp_year}
                                            onChange={e =>
                                                handleFormChange(
                                                    e,
                                                    'cardDetails',
                                                )
                                            }
                                        />
                                    </div>
                                    <div className="relative flex w-1/2 border-l border-gray-300">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            data-slot="icon"
                                            className="absolute right-0 top-1/2 h-6 w-6 -translate-x-1/2 -translate-y-1/2 transform text-gray-500"
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
                                            className="w-full border-none bg-gray-100 p-3 outline-none ring-0 focus:bg-gray-200 focus:ring-0"
                                            placeholder="CVV"
                                            maxLength={4}
                                            value={cardDetails.card_cvn}
                                            onChange={e =>
                                                handleFormChange(
                                                    e,
                                                    'cardDetails',
                                                )
                                            }
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
                        <div className="col-span-6 flex items-center gap-x-4 rounded-md border border-gray-300 p-4 text-sm font-medium">
                            <label
                                htmlFor="save-card-checkbox"
                                className="order-2"
                            >
                                Save my information for faster checkout
                            </label>
                            <input
                                id="save-card-checkbox"
                                type="checkbox"
                                checked={isMultiUse ? true : false}
                                onChange={() => setIsMultiUse(!isMultiUse)}
                            />
                        </div>
                        <div className="mt-4 flex flex-col gap-4">
                            <button
                                className={`w-full rounded-md bg-black py-3 text-sm font-bold uppercase text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-black`}
                                disabled={authenticating ? true : false}
                                onClick={payWithCard}
                            >
                                Charge Card
                            </button>
                        </div>
                    </div>

                    {/* e-Wallet payment */}
                    <div
                        className={`grid w-full grid-cols-6 gap-4 rounded-bl-md rounded-br-md bg-white p-8 shadow-sm ${
                            paymentMethod === 'ewallet' ? 'flex' : 'hidden'
                        }`}
                    >
                        <input
                            placeholder="Amount to pay"
                            type="text"
                            className="col-span-6 mb-2 rounded-md border border-gray-300"
                            value={amount}
                            onChange={e => setAmount(e.target.value)}
                        ></input>
                        <button
                            className={`col-span-6 rounded-md bg-black py-3 text-sm font-bold uppercase text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-green-600`}
                            onClick={payWithEwallet}
                        >
                            Charge with eWallet
                        </button>
                    </div>
                </div>

                {/* API Response */}
                {apiResponse !== '' || errorMessage !== '' ? (
                    <div className="my-2 flex w-[500px] flex-col gap-4 whitespace-nowrap rounded-md border border-gray-300 bg-white p-8 shadow-md">
                        <span className="mb-2 text-lg font-bold">
                            Xendit API Response
                        </span>

                        {apiResponse !== '' ? (
                            <>
                                {isMultiUse ? (
                                    <span className="mb-2 flex items-center gap-4 whitespace-pre-wrap text-sm">
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
                                            If you choose to save this card for
                                            future transactions, make sure to
                                            store the{' '}
                                            <code className="rounded bg-gray-200 px-2 py-1 text-xs">
                                                credit_card_token_id
                                            </code>{' '}
                                            in your database. This token is
                                            necessary for future charges without
                                            re-entering card details.
                                        </span>
                                    </span>
                                ) : (
                                    ''
                                )}
                                <pre className="whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-xs leading-relaxed">
                                    {apiResponse}
                                </pre>
                            </>
                        ) : (
                            ''
                        )}

                        {errorMessage !== '' ? (
                            <pre className="whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-xs leading-relaxed">
                                {errorMessage}
                            </pre>
                        ) : (
                            ''
                        )}

                        {errors && errors.errors ? (
                            <pre className="whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-xs leading-relaxed">
                                {errors.errors.map((error, index) => (
                                    <span
                                        key={index}
                                        className="flex w-full justify-between"
                                    >
                                        <span>{error.path}</span>
                                        <span>{error.message}</span>
                                    </span>
                                ))}
                            </pre>
                        ) : (
                            ''
                        )}

                        {apiResponse === '' && errorMessage === '' ? (
                            <pre className="whitespace-pre-wrap rounded-md bg-gray-100 p-4 text-center text-xs leading-relaxed">
                                There's no response yet from Xendit.
                            </pre>
                        ) : (
                            ''
                        )}
                    </div>
                ) : null}
            </div>
        </>
    )
}

export default Checkout
