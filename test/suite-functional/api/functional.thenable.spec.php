<?php

declare (strict_types = 1); // @codeCoverageIgnore

namespace Recoil;

use Eloquent\Phony\Phony;

context('when it has a then method', function () {
    rit('resumes the strand when resolved', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {
                    $resolve('<value>');
                },
            ]
        );

        expect(yield $promise->get())->to->equal('<value>');
    });

    rit('resumes the strand with an exception when rejected', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {
                    $reject(new \Exception('<rejected>'));
                },
            ]
        );

        try {
            yield $promise->get();
            expect(false)->to->equal('Expected exception was not thrown.');
        } catch (\Exception $e) {
            expect($e->getMessage())->to->equal('<rejected>');
        }
    });

    rit('resumes the strand with a value when rejected', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {
                    $reject('<rejected>');
                },
            ]
        );

        try {
            yield $promise->get();
            expect(false)->to->equal('Expected exception was not thrown.');
        } catch (\Exception $e) {
            expect($e->getMessage())->to->equal('<rejected>');
        }
    });

    rit('terminates the strand when cancelled', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {},
                'cancel' => function () {},
            ]
        );

        $strand = yield Recoil::execute(function () use ($promise) {
            yield $promise->get();
        });

        yield;

        $strand->terminate();

        $promise->cancel->called();
    });
});

context('when it has both then and done methods', function () {
    rit('resumes the strand when resolved', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {},
                'done' => function (callable $resolve, callable $reject) {
                    $resolve('<value>');
                },
            ]
        );

        expect(yield $promise->get())->to->equal('<value>');
    });

    rit('resumes the strand with an exception when rejected', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {},
                'done' => function (callable $resolve, callable $reject) {
                    $reject(new \Exception('<rejected>'));
                },
            ]
        );

        try {
            yield $promise->get();
            expect(false)->to->equal('Expected exception was not thrown.');
        } catch (\Exception $e) {
            expect($e->getMessage())->to->equal('<rejected>');
        }
    });

    rit('resumes the strand with a value when rejected', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {},
                'done' => function (callable $resolve, callable $reject) {
                    $reject('<rejected>');
                },
            ]
        );

        try {
            yield $promise->get();
            expect(false)->to->equal('Expected exception was not thrown.');
        } catch (\Exception $e) {
            expect($e->getMessage())->to->equal('<rejected>');
        }
    });

    rit('terminates the strand when cancelled', function () {
        $promise = Phony::partialMock(
            [
                'then' => function (callable $resolve, callable $reject) {},
                'done' => function (callable $resolve, callable $reject) {},
                'cancel' => function () {},
            ]
        );

        $strand = yield Recoil::execute(function () use ($promise) {
            yield $promise->get();
        });

        yield;

        $strand->terminate();

        $promise->cancel->called();
    });
});
