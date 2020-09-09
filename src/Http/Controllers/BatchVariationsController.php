<?php

namespace Bazar\Http\Controllers;

use Bazar\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class BatchVariationsController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = Arr::dot($request->except('ids'));

        $data = collect($data)->filter()->mapWithKeys(function ($item, $key) {
            return [str_replace('.', '->', $key) => $item];
        })->all();

        $product->variations()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->update($data);

        return Redirect::back()->with(
            'message', __(':count variations have been updated.', ['count' => count($ids)])
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $variations = $product->variations()->withTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        );

        $request->has('force') ? $variations->forceDelete() : $variations->delete();

        return Redirect::back()->with(
            'message', __(':count variations have been deleted.', ['count' => count($ids)])
        );
    }

    /**
     * Restore the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Bazar\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Request $request, Product $product): RedirectResponse
    {
        $product->variations()->onlyTrashed()->whereIn(
            'id', $ids = $request->input('ids', [])
        )->restore();

        return Redirect::back()->with(
            'message', __(':count variations have been restored.', ['count' => count($ids)])
        );
    }
}