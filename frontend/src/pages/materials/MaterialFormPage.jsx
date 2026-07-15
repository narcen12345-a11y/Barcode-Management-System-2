import { useNavigate, useParams } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useFormSubmit } from '../../hooks/useFormSubmit';
import { materialService } from '../../services/materialService';
import { materialTypeService } from '../../services/materialTypeService';
import { materialModelService } from '../../services/materialModelService';
import { PageHeader } from '../../components/ui/PageHeader';
import { Spinner } from '../../components/ui/Spinner';

const schema = z.object({
  material_type_id: z.string().min(1, 'Material Type wajib dipilih'),
  material_model_id: z.string().min(1, 'Material Model wajib dipilih'),
  material_code: z.string().min(1, 'Material Code wajib diisi').max(50, 'Maksimal 50 karakter'),
  name: z.string().min(1, 'Nama wajib diisi').max(255, 'Maksimal 255 karakter'),
  description: z.string().optional().or(z.literal('')),
  is_active: z.boolean().optional().default(true),
});

export function MaterialFormPage() {
  const { id } = useParams();
  const isEdit = !!id;
  const navigate = useNavigate();
  const [loadingData, setLoadingData] = useState(isEdit);

  // Dropdown data
  const [materialTypes, setMaterialTypes] = useState([]);
  const [materialModels, setMaterialModels] = useState([]);
  const [loadingTypes, setLoadingTypes] = useState(true);
  const [loadingModels, setLoadingModels] = useState(false);

  const form = useFormSubmit({
    service: materialService,
    method: isEdit ? 'update' : 'create',
    id,
    queryKey: 'materials',
    onSuccess: () => navigate('/materials'),
  });

  const {
    register,
    handleSubmit,
    formState: { errors },
    reset,
    watch,
  } = useForm({
    resolver: zodResolver(schema),
    defaultValues: {
      is_active: true,
    },
  });

  const selectedTypeId = watch('material_type_id');

  // Load material types on mount
  useEffect(() => {
    materialTypeService
      .getAllUnpaginated()
      .then((res) => {
        const items = res.data || res || [];
        setMaterialTypes(items);
      })
      .finally(() => setLoadingTypes(false));
  }, []);

  // Load material models when type changes (dependent dropdown)
  useEffect(() => {
    if (!selectedTypeId) {
      setMaterialModels([]);
      return;
    }
    setLoadingModels(true);
    materialModelService
      .getByMaterialType(selectedTypeId)
      .then((res) => {
        const items = res.data || res || [];
        setMaterialModels(items);
      })
      .finally(() => setLoadingModels(false));
  }, [selectedTypeId]);

  // Load existing data for edit
  useEffect(() => {
    if (isEdit) {
      materialService
        .getById(id)
        .then((res) => {
          const data = res.data || res;
          reset({
            material_type_id: String(data.material_type_id || ''),
            material_model_id: String(data.material_model_id || ''),
            material_code: data.material_code || '',
            name: data.name || '',
            description: data.description || '',
            is_active: data.is_active ?? true,
          });
        })
        .finally(() => setLoadingData(false));
    }
  }, [id, isEdit, reset]);

  const onSubmit = async (data) => {
    await form.submit({
      ...data,
      material_type_id: Number(data.material_type_id),
      material_model_id: Number(data.material_model_id),
    });
  };

  if (loadingData) {
    return (
      <div className="flex items-center justify-center py-20">
        <Spinner size="lg" />
      </div>
    );
  }

  return (
    <div>
      <PageHeader
        title={isEdit ? 'Edit Material' : 'Tambah Material'}
        description={isEdit ? 'Perbarui data material' : 'Buat data material baru'}
      />

      <form onSubmit={handleSubmit(onSubmit)} className="max-w-2xl space-y-5">
        {/* Material Type */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Material Type <span className="text-red-400">*</span>
          </label>
          {loadingTypes ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat tipe material...
            </div>
          ) : (
            <select
              {...register('material_type_id')}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
            >
              <option value="">Pilih Material Type</option>
              {materialTypes.map((mt) => (
                <option key={mt.id} value={mt.id}>
                  {mt.name}
                </option>
              ))}
            </select>
          )}
          {errors.material_type_id && (
            <p className="mt-1 text-xs text-red-400">{errors.material_type_id.message}</p>
          )}
          {form.errors?.material_type_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.material_type_id}</p>
          )}
        </div>

        {/* Material Model (dependent dropdown) */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Material Model <span className="text-red-400">*</span>
          </label>
          {loadingModels ? (
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <Spinner size="sm" /> Memuat model material...
            </div>
          ) : (
            <select
              {...register('material_model_id')}
              className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
              disabled={!selectedTypeId}
            >
              <option value="">
                {selectedTypeId ? 'Pilih Material Model' : 'Pilih Material Type terlebih dahulu'}
              </option>
              {materialModels.map((mm) => (
                <option key={mm.id} value={mm.id}>
                  {mm.name}
                </option>
              ))}
            </select>
          )}
          {errors.material_model_id && (
            <p className="mt-1 text-xs text-red-400">{errors.material_model_id.message}</p>
          )}
          {form.errors?.material_model_id && (
            <p className="mt-1 text-xs text-red-400">{form.errors.material_model_id}</p>
          )}
        </div>

        {/* Material Code */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Material Code <span className="text-red-400">*</span>
          </label>
          <input
            {...register('material_code')}
            placeholder="Contoh: MAT-001"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.material_code && (
            <p className="mt-1 text-xs text-red-400">{errors.material_code.message}</p>
          )}
          {form.errors?.material_code && (
            <p className="mt-1 text-xs text-red-400">{form.errors.material_code}</p>
          )}
        </div>

        {/* Name */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">
            Name <span className="text-red-400">*</span>
          </label>
          <input
            {...register('name')}
            placeholder="Nama material"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.name && (
            <p className="mt-1 text-xs text-red-400">{errors.name.message}</p>
          )}
          {form.errors?.name && (
            <p className="mt-1 text-xs text-red-400">{form.errors.name}</p>
          )}
        </div>

        {/* Description */}
        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-300">Description</label>
          <textarea
            {...register('description')}
            rows={3}
            placeholder="Deskripsi material"
            className="w-full rounded-lg border border-slate-700 bg-slate-800 px-4 py-2.5 text-sm text-slate-100 placeholder-slate-500 outline-none transition focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500"
          />
          {errors.description && (
            <p className="mt-1 text-xs text-red-400">{errors.description.message}</p>
          )}
          {form.errors?.description && (
            <p className="mt-1 text-xs text-red-400">{form.errors.description}</p>
          )}
        </div>

        {/* Is Active */}
        <div>
          <label className="flex items-center gap-3">
            <input
              type="checkbox"
              {...register('is_active')}
              className="h-4 w-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500"
            />
            <span className="text-sm font-medium text-slate-300">Active</span>
          </label>
        </div>

        {/* Actions */}
        <div className="flex items-center gap-3 pt-2">
          <button
            type="submit"
            disabled={form.loading}
            className="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-6 py-2.5 text-sm font-medium text-white transition hover:bg-cyan-600 disabled:cursor-not-allowed disabled:opacity-50"
          >
            {form.loading ? (
              <>
                <Spinner size="sm" />
                Menyimpan...
              </>
            ) : (
              'Simpan'
            )}
          </button>
          <button
            type="button"
            onClick={() => navigate('/materials')}
            className="rounded-lg border border-slate-700 px-6 py-2.5 text-sm font-medium text-slate-300 transition hover:bg-slate-800"
          >
            Batal
          </button>
        </div>
      </form>
    </div>
  );
}
