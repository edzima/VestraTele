import {ExtraParam} from "@/types/ExtraParam";

export function mapExtraParamsToObj(extraParams:ExtraParam[]): Object{
    const res: Object = {};
    extraParams.forEach((param)=>{
        res[param.name] =  param.value
    });
    return res;
}
