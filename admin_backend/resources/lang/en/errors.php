<?php

use App\Helpers\ResponseError;

$e = new ResponseError;

return [

    /*
    |--------------------------------------------------------------------------
    | Pagination Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the paginator library to build
    | the simple pagination links. You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    $e::NO_ERROR  => 'Successfully',
    $e::ERROR_100 => 'User is not logged in.',
    $e::ERROR_101 => 'User does not have the right roles.',
    $e::ERROR_102 => 'Login or password is incorrect.',
    $e::ERROR_103 => 'User email address is not verified.',
    $e::ERROR_104 => 'User phone number is not verified.',
    $e::ERROR_105 => 'User account is not verified.',
    $e::ERROR_106 => 'User already exists.',
    $e::ERROR_107 => 'Please login using facebook or google.',
    $e::ERROR_108 => 'The user does not have a Wallet.',
    $e::ERROR_109 => 'Insufficient wallet balance.',
    $e::ERROR_110 => 'Can\'t update this user role.',
    $e::ERROR_111 => 'You can buy only :quantity products.',
    $e::ERROR_112 => 'when status: :verify you should add text $verify_code on body and alt body',
    $e::ERROR_113 => 'Deliveryman doesn\'t have Wallet',
    $e::ERROR_114 => 'Seller doesn\'t have Wallet',
    $e::ERROR_115 => 'Phone not found',

    $e::ERROR_201 => 'Wrong OTP Code',
    $e::ERROR_202 => 'Too many request, try later',
    $e::ERROR_203 => 'OTP code is expired',

    $e::ERROR_204 => 'You are not seller yet or your shop is not created',
    $e::ERROR_205 => 'Shop already created',
    $e::ERROR_206 => 'User already has Shop',
    $e::ERROR_207 => 'Can\'t update Shop Seller',
    $e::ERROR_208 => 'Subscription already active',
    $e::ERROR_209 => 'Shop delivery zone already created',
    $e::ERROR_210 => 'Delivery already attached',
    $e::ERROR_211 => 'invalid deliveryman or token not found',
    $e::ERROR_212 => 'Not your shop. Check your other account',
    $e::ERROR_213 => 'Your subscription is expired at',
    $e::ERROR_214 => 'Your subscription product limit has expired',
    $e::ERROR_215 => 'Incorrect code or token expired',
    $e::ERROR_216 => 'Verify code send',
    $e::ERROR_217 => 'User send email',

    $e::ERROR_249 => 'Invalid Coupon',
    $e::ERROR_250 => 'Coupon expired',
    $e::ERROR_251 => 'Coupon already used',
    $e::ERROR_252 => 'Status already used',
    $e::ERROR_253 => 'Wrong status type',
    $e::ERROR_254 => 'Can\'t update Cancel status',
    $e::ERROR_255 => 'Can\'t update Order status if order already on a way or delivered',

    $e::ERROR_400 => 'Bad request.',
    $e::ERROR_401 => 'Unauthorized.',
    $e::ERROR_403 => 'Your project is not activated.',
    $e::ERROR_404 => 'Item\'s not found.',
    $e::ERROR_415 => 'No connection to database',
    $e::ERROR_422 => 'Validation Error',
    $e::ERROR_429 => 'Too many requests',
    $e::ERROR_430 => 'Stock quantity 0',
    $e::ERROR_431 => 'Active default currency not found',
    $e::ERROR_430 => 'Stock quantity 0',
    $e::ERROR_432 => 'Undefined Type',

    $e::ERROR_501 => 'Error during creating',
    $e::ERROR_502 => 'Error during updating',
    $e::ERROR_503 => 'Error during deleting.',
    $e::ERROR_504 => 'Can\'t delete record that has values.',
    $e::ERROR_505 => 'Can\'t delete default record. # :ids',
    $e::ERROR_506 => 'Already exists.',
    $e::ERROR_507 => 'Can\'t delete record that has products.',
    $e::ERROR_508 => 'Excel format incorrect or data invalid.',
    $e::ERROR_509 => 'Invalid date format.',
    $e::ERROR_510 => 'Address in correct.',


    $e::CONFIRMATION_CODE               => 'Confirmation code :code',
    $e::NEW_ORDER                       => 'New order for you # :id',
    $e::PHONE_OR_EMAIL_NOT_FOUND        => 'Phone or Email not found',
    $e::ORDER_NOT_FOUND                 => 'Order not found',
    $e::ORDER_REFUNDED                  => 'Order refunded',
    $e::ORDER_PICKUP                    => 'Order is pickup',
    $e::SHOP_NOT_FOUND                  => 'Shop not found',
    $e::OTHER_SHOP                      => 'Other shop',
    $e::SHOP_OR_DELIVERY_ZONE           => 'Empty shop or delivery zone',
    $e::NOT_IN_POLYGON                  => 'Not in polygon',
    $e::CURRENCY_NOT_FOUND              => 'Currency not found',
    $e::LANGUAGE_NOT_FOUND              => 'Language not found',
    $e::CANT_DELETE_ORDERS              => 'Can`t delete orders :ids',
    $e::CANT_UPDATE_ORDERS              => 'Can`t update orders :ids',
    $e::STATUS_CHANGED                  => 'Your order status has been changed to :status',
    $e::PAYOUT_ACCEPTED                 => 'Payout already :status',
    $e::CANT_DELETE_IDS                 => 'Can`t delete :ids',
    $e::USER_NOT_FOUND                  => 'User not found',
    $e::USER_IS_BANNED                  => 'User is banned!',
    $e::INCORRECT_LOGIN_PROVIDER        => 'Please login using facebook or google.',
    $e::FIN_FO                          => 'You need on php file info extension',
    $e::USER_SUCCESSFULLY_REGISTERED    => 'User successfully registered',
    $e::USER_CARTS_IS_EMPTY             => 'User carts is empty',
    $e::PRODUCTS_IS_EMPTY               => 'Products is empty',
    $e::RECORD_WAS_SUCCESSFULLY_CREATED => 'Record was successfully created',
    $e::RECORD_WAS_SUCCESSFULLY_UPDATED => 'Record was successfully updated',
    $e::RECORD_WAS_SUCCESSFULLY_DELETED => 'Record was successfully deleted',
    $e::IMAGE_SUCCESSFULLY_UPLOADED     => 'Success :title, :type',
    $e::EMPTY_STATUS                    => 'Status is empty',
    $e::SUCCESS                         => 'Success',
    $e::DELIVERYMAN_IS_NOT_CHANGED      => 'You need change delivery man',
    $e::CATEGORY_IS_PARENT              => 'Category is parent',
    $e::ATTACH_FOR_ADDON                => 'You can`t attach products for addon',
    $e::TYPE_PRICE_USER                 => 'Type, price or user is empty',
    $e::NOTHING_TO_UPDATE               => 'Nothing to update',

    $e::ORDER_OR_DELIVERYMAN_IS_EMPTY   => 'Order not found or deliveryman is not attached',
];
