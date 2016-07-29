<?php

foreach ( $_POST as $key => $value ) {
    // assign to temporary variable and strip whitespace if not an array
    $temp = is_array( $value ) ? $value : trim( $value );

    // add expected variables to array
    if ( in_array( $key, $expected ) ) {
        // otherwise, assign to a variable of the same name as $key
        ${$key} = $temp;
    }
}

// strip spaces and everything that is not a number
function remove_nonnumeric( $value ) {
    $value = preg_replace( '/[^0-9]*/i', '', $value );

    return $value;
}

// validate the user's email
if ( !empty( $email ) ) {
    $valid_email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ( $valid_email ) {
        $headers .= "\r\nReply-To: $valid_email";
    } else {
        $errors['email'] = true;
    }
}

// format phone

if ( !empty( $phone ) ) {

    function format_phone( $value ) {
        $extension = NULL;
        $complete_phone = NULL;

        // takes a ten diget string and formats in into (000) 000-0000
        function create_phone( $phone ) {
            $formatted_phone = NULL;

            // break apart phone string
            $phone = preg_split('//', $phone);

            // format (000) 000-0000
            $formatted_phone = '(' . $phone[1] . $phone[2] . $phone[3] . ') ' . $phone[4] . $phone[5] . $phone[6] . '-' . $phone[7] . $phone[8] . $phone[9] . $phone[10];

            return $formatted_phone;
        }

        // remove whitespace
        $value = trim( $value );

        // look for extension preceeded by 'x'
        // split phone number and extension into two seperate strings
        if ( strpos($value, 'x') != FALSE ) {
            $complete_phone = preg_split( '/[x]/i', $value );

            $phone = $complete_phone[0];
            $extension = $complete_phone[1];

            $phone = remove_nonnumeric( $phone );

            // strip spaces and everything that is not a number
            $extension = remove_nonnumeric( $extension );
        } else {
            $phone = $value;

            // strip spaces and everything that is not a number
            $phone = remove_nonnumeric( $phone );

            // strip leading 1
            $phone = preg_replace('/\b[1]/', '', $phone);
        }

        if ( !( is_null( $extension ) ) ) {
            $formatted_phone = create_phone( $phone ) . 'x' . $extension;
        } else {
            $formatted_phone = create_phone( $phone );
        }

        return $formatted_phone;
    }

    $phone = format_phone( $phone );
}

$mail_sent = false;

// initialize the $message variable
$message = '';

// loop through the $expected array
foreach( $expected as $item ) {
    // assign the value of the current item to $val
    if ( isset( ${$item} ) && !empty( ${$item} ) ) {
        $val = ${$item};
    } else {
        // if it has no value, assign 'Not selected'
        $val = 'Not Selected';
    }

    // if an array, expand as comma-separated string
    if ( is_array( $val ) ) {
        $val = implode( ', ', $val );
    }

    // replace underscores and hyphens in the label with spaces
    $item = str_replace( array( '_', '-' ), ' ', $item );

    // add label and value to the message body
    $message .= ucfirst( $item ).": $val\r\n\r\n";
}

// limit line length to 70 characters
$message = wordwrap( htmlentities( $message ) , 70);

$mail_sent = mail( $to, $subject, $message, $headers );

if ( !$mail_sent ) {
    $errors['mail_fail'] = true;
}